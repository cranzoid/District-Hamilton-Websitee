<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'Order Management';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Order #')
                            ->disabled(),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready',
                                'out_for_delivery' => 'Out for Delivery',
                                'completed' => 'Completed',
                                'canceled' => 'Canceled',
                            ])
                            ->required(),
                            
                        Forms\Components\Select::make('order_type')
                            ->options([
                                'pickup' => 'Pickup',
                                'delivery' => 'Delivery',
                            ])
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('pickup_time')
                            ->label('Pickup/Delivery Time'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('customer_phone')
                            ->tel()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('delivery_address')
                            ->visible(fn (callable $get) => $get('order_type') === 'delivery')
                            ->maxLength(65535),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('tax')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('tip_amount')
                            ->label('Tip')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('delivery_fee')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->visible(fn (callable $get) => $get('order_type') === 'delivery'),
                            
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                            
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                            
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('gift_card_code_used')
                            ->label('Gift Card Used')
                            ->disabled(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('order_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'pickup',
                        'warning' => 'delivery',
                    ]),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'preparing',
                        'success' => 'ready',
                        'info' => 'out_for_delivery',
                        'danger' => 'canceled',
                        'secondary' => 'completed',
                    ]),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('pickup_time')
                    ->label('Pickup/Delivery Time')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'preparing' => 'Preparing',
                        'ready' => 'Ready',
                        'out_for_delivery' => 'Out for Delivery',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                    ]),
                    
                Tables\Filters\SelectFilter::make('order_type')
                    ->options([
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                    ]),
                    
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->url(fn (Order $record): string => route('orders.print', $record))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-printer'),
                Tables\Actions\Action::make('mark_preparing')
                    ->label('Confirm')
                    ->icon('heroicon-o-fire')
                    ->color('warning')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $oldStatus = $record->status;
                        $record->update(['status' => 'preparing']);
                        Notification::route('mail', $record->customer_email)
                            ->notify(new OrderStatusChanged($record, $oldStatus));
                        FilamentNotification::make()->title('Order marked as Preparing')->success()->send();
                    }),
                Tables\Actions\Action::make('mark_ready')
                    ->label('Ready')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'preparing')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $oldStatus = $record->status;
                        $record->update(['status' => 'ready']);
                        Notification::route('mail', $record->customer_email)
                            ->notify(new OrderStatusChanged($record, $oldStatus));
                        FilamentNotification::make()->title('Order marked as Ready')->success()->send();
                    }),
                Tables\Actions\Action::make('mark_out_for_delivery')
                    ->label('Out for Delivery')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status === 'ready' && $record->order_type === 'delivery')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $oldStatus = $record->status;
                        $record->update(['status' => 'out_for_delivery']);
                        Notification::route('mail', $record->customer_email)
                            ->notify(new OrderStatusChanged($record, $oldStatus));
                        FilamentNotification::make()->title('Order marked as Out for Delivery')->success()->send();
                    }),
                Tables\Actions\Action::make('mark_completed')
                    ->label('Complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Order $record) => in_array($record->status, ['ready', 'out_for_delivery']))
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $oldStatus = $record->status;
                        $record->update(['status' => 'completed']);
                        Notification::route('mail', $record->customer_email)
                            ->notify(new OrderStatusChanged($record, $oldStatus));
                        FilamentNotification::make()->title('Order completed')->success()->send();
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Order $record) => !in_array($record->status, ['completed', 'canceled']))
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $oldStatus = $record->status;
                        $record->update(['status' => 'canceled']);
                        Notification::route('mail', $record->customer_email)
                            ->notify(new OrderStatusChanged($record, $oldStatus));
                        FilamentNotification::make()->title('Order cancelled')->warning()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'preparing' => 'Preparing',
                                    'ready' => 'Ready',
                                    'out_for_delivery' => 'Out for Delivery',
                                    'completed' => 'Completed',
                                    'canceled' => 'Canceled',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => $data['status'],
                                ]);
                            }
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(route('admin.orders.export'))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
