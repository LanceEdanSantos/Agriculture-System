<x-mail::message>
# New Message on Your Item Request

Hello {{ $message->itemRequest->user->name }},

You have received a new message regarding your item request #{{ $message->item_request_id }}.

**From:** {{ $message->user->name }}

**Message:**
{{ $message->message }}

**Request Details:**
- Item: {{ $message->itemRequest->inventoryItem->name ?? 'N/A' }}
- Requested Quantity: {{ $message->itemRequest->requested_quantity }}
- Status: {{ ucfirst($message->itemRequest->status) }}

<x-mail::button :url="url('/item-requests/' . $message->item_request_id)">
View Request & Reply
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
