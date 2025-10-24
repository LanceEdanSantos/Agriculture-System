# Quick Start: Request Messages / Chat Feature

## Installation Steps

### 1. Run Migration
```bash
php artisan migrate
```
This creates the `request_messages` table.

### 2. Clear Cache (Optional)
```bash
php artisan optimize:clear
```

## How to Use

### Two Interfaces Available:

1. **Filament Admin Panel** - Full-featured with advanced filtering and management
2. **Livewire Frontend** - Simple chat interface for farmers and admins

### For Admins/Managers (Filament Admin Panel):

#### Scenario: Farmer requests 50 items, but only 30 available

1. **Navigate to Item Requests** (Admin Panel → Inventory → Item Requests)
2. **Click on a pending request** to view details
3. You'll see:
   - Request information
   - **Available Stock** highlighted in red if insufficient
   - A new "Request Discussion" tab at the bottom

4. **Option A: Send Message Before Approval**
   - Click "Request Discussion" tab
   - Click "Send Message" button
   - Type: "Only 30 units are available. Do you want to proceed with 30 or wait?"
   - Click "Send"
   - Wait for farmer's response (table auto-updates every 5 seconds)

5. **Option B: Approve with Message**
   - Click "Approve" action (green button)
   - Modal shows:
     - Stock availability warning
     - Approved quantity field (defaults to min of requested/available)
     - "Message to Farmer" field
   - Enter message: "Only 30 available currently. 20 more arrive next week."
   - Enter approved quantity: 30
   - Click "Approve"
   - System automatically:
     - Creates stock movement
     - Sends your message to farmer
     - Sends approval confirmation message

### For Farmers (Livewire Frontend):

1. **Create a request** as usual
2. **View your request** to see status
3. **Scroll down to "Request Discussion" section** - it's now directly on the page!
4. **Type your message** in the text area
5. **Click "Send Message"** to send
6. **Messages update automatically** every 5 seconds
7. See admin messages with green shield icon 🛡️
8. Your messages appear with blue user icon 👤

## Features at a Glance

✅ **Real-time chat** with 5-second polling  
✅ **Stock availability** clearly displayed  
✅ **Automatic messages** on approve/reject  
✅ **Message history** with timestamps  
✅ **Read/unread indicators**  
✅ **Filter messages** by type or status  
✅ **Admin/Farmer differentiation** with icons  

## Key Points

- Messages are **tied to specific requests**
- **Admins** have a shield icon (🛡️), **farmers** have user icon (👤)
- Messages **persist** even after request is approved/rejected
- All messages are **activity logged** for audit trail
- Farmers can **only see their own** request messages
- Admins can see **all request** messages

## Testing

1. Start your database server (MySQL)
2. Run migration: `php artisan migrate`
3. Login as admin
4. Create or open an item request
5. Go to "Request Discussion" tab
6. Click "Send Message" and test the chat
7. Test approve/reject with messages

## Troubleshooting

**Q: Messages not appearing?**  
A: Check that polling is working (table should refresh every 5 seconds)

**Q: Can't send messages?**  
A: Ensure you have proper permissions and the request exists

**Q: Migration fails?**  
A: Make sure your database is running and credentials are correct in `.env`

---

For complete documentation, see: `docs/REQUEST_MESSAGES_FEATURE.md`
