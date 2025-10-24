# Request Messages / Chat Feature

## Overview
The Request Messages feature provides a real-time chat-like interface for communication between administrators and farmers regarding item requests. This allows admins to explain stock availability and negotiate quantities before final approval.

## Features

### 1. **Real-Time Communication**
- Chat interface with polling (updates every 5 seconds)
- Both admin and farmers can send messages
- Messages are displayed chronologically
- Unread message indicators

### 2. **Stock Availability Messaging**
When an admin reviews a request:
- If requested quantity exceeds available stock (e.g., 50 requested but only 30 available)
- Admin can send a message to farmer: "Only 30 items are available. Do you want to proceed with 30?"
- Farmer can respond through the chat
- Admin can then approve with adjusted quantity

### 3. **Approval with Messaging**
The approve action includes:
- **Stock Availability Display**: Shows requested vs available stock with visual indicators
- **Approved Quantity Field**: Pre-filled with minimum of requested/available
- **Message to Farmer**: Optional field to explain stock situation
- **Internal Notes**: For admin records (not visible to farmer)
- **Automatic Messages**: System sends confirmation message after approval

### 4. **Rejection with Messaging**
The reject action includes:
- **Rejection Reason**: Required field explaining why request was rejected
- **Additional Message**: Optional message with suggestions or alternatives
- **Automatic Notification**: Farmer receives rejection message automatically

### 5. **Message Management**
- **Mark as Read**: Users can mark messages as read
- **Delete**: Users can delete their own messages (admins can delete any)
- **Filter**: Filter by message type (Admin/Farmer) or unread status
- **View Full Message**: Click to view complete message content

## Database Schema

### `request_messages` Table
```sql
- id (bigint)
- item_request_id (foreign key to item_requests)
- user_id (foreign key to users)
- message (text)
- is_admin_message (boolean) - Auto-detected based on user role
- read_at (timestamp nullable)
- created_at (timestamp)
- updated_at (timestamp)

Indexes:
- (item_request_id, created_at)
- (user_id, created_at)
```

## Model Relationships

### ItemRequest Model
```php
public function messages(): HasMany
{
    return $this->hasMany(RequestMessage::class);
}
```

### RequestMessage Model
```php
public function itemRequest(): BelongsTo
{
    return $this->belongsTo(ItemRequest::class);
}

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

## Usage Example

### Scenario: Insufficient Stock
1. **Farmer requests 50 units** of fertilizer
2. **Admin views request** and sees only 30 units available
3. **Admin clicks "Approve"** button
   - Modal shows stock availability warning
   - Approved quantity is pre-filled with 30
   - Admin types message: "Only 30 units available currently. 20 more will arrive next week. Do you want 30 now or wait for full quantity?"
4. **Message is sent** to farmer via chat interface
5. **Farmer responds** through chat: "I'll take 30 now, please"
6. **Admin approves** the request with 30 units
7. **Automatic message** sent: "Your request has been approved for 30 units. Items will be prepared for delivery."

## UI Components

### View Item Request Page
The view page displays:
1. **Request Information Section**
   - Requested by, Farm, Status
2. **Item Details Section**
   - Item name, Requested quantity, **Available stock** (with color coding)
   - Helper text warns if insufficient stock
3. **Timeline Section**
   - Requested at, Approved at, Delivered at
   - Approved by, Rejection reason (if rejected)
4. **Request Discussion Tab**
   - Chat interface with messages
   - "Send Message" button to create new message
   - Real-time polling updates

### Messages Interface Columns
- **From**: Shows sender name with icon (shield for admin, user for farmer)
- **Message**: The message content (truncated, click to view full)
- **Read**: Check/clock icon showing read status
- **Sent At**: Timestamp with relative time (e.g., "5 minutes ago")

## Permissions
- **Farmers**: Can view and send messages on their own requests
- **Admins/Managers**: Can view and send messages on all requests
- **Delete**: Users can delete their own messages; admins can delete any

## Technical Details

### Polling Configuration
```php
protected static ?string $pollingInterval = '5s';
```
The table automatically refreshes every 5 seconds to show new messages.

### Auto-Detection of Admin Messages
```php
static::creating(function ($message) {
    if (empty($message->is_admin_message) && Auth::check()) {
        $user = Auth::user();
        $message->is_admin_message = $user->hasRole('super_admin') || $user->hasRole('farm_manager');
    }
});
```

## Migration
Run the migration to create the `request_messages` table:
```bash
php artisan migrate
```

The migration file: `2025_10_24_020859_create_request_messages_table.php`

## Files Modified/Created

### Created:
1. `app/Models/RequestMessage.php` - Model for messages
2. `database/migrations/2025_10_24_020859_create_request_messages_table.php` - Migration
3. `app/Filament/Resources/ItemRequestResource/RelationManagers/MessagesRelationManager.php` - Chat interface

### Modified:
1. `app/Models/ItemRequest.php` - Added messages relationship
2. `app/Filament/Resources/ItemRequestResource.php` - Enhanced approve/reject actions with messaging
3. `app/Filament/Resources/ItemRequestResource/Pages/ViewItemRequest.php` - Added infolist with stock availability display

## Benefits

1. **Transparency**: Clear communication about stock availability
2. **Efficiency**: Resolve questions without phone calls or separate messages
3. **Documentation**: All conversations are recorded with timestamps
4. **Real-time**: Polling ensures both parties see updates quickly
5. **User-friendly**: Chat interface is familiar and easy to use
6. **Audit Trail**: Activity logging tracks all message changes

## Future Enhancements (Optional)

- Email notifications when new messages arrive
- WebSocket support for true real-time updates (instead of polling)
- File attachment support in messages
- Message reactions (thumbs up, etc.)
- Typing indicators
- Read receipts for all messages
