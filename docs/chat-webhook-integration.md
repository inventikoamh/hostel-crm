# Chat System - n8n Webhook Integration

## Overview
The Hostel CRM Chat System sends user messages and user information to your n8n webhook at `https://n8n.admin.inventiko.com/webhook-test/crm` and expects a specific JSON response format.

## Request Format (Sent to n8n)

### POST Request to: `https://n8n.admin.inventiko.com/webhook-test/crm`

```json
{
    "message": "Show me available rooms for next month",
    "conversation_id": "conv_1703123456789_abc123def",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "is_tenant": true,
        "is_super_admin": false,
        "status": "active",
        "last_login_at": "2024-01-15T10:30:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "tenant_profile": {
            "id": 1,
            "phone": "+1234567890",
            "date_of_birth": "1990-05-15",
            "address": "123 Main St, City, State",
            "occupation": "Software Developer",
            "company": "Tech Corp",
            "status": "active",
            "move_in_date": "2024-01-01",
            "move_out_date": null,
            "monthly_rent": 500.00,
            "lease_start_date": "2024-01-01",
            "lease_end_date": "2024-12-31",
            "is_verified": true
        }
    },
    "timestamp": "2024-01-15T10:30:00.000000Z",
    "session_id": "laravel_session_abc123"
}
```

## Expected Response Format (From n8n)

### Success Response
```json
{
    "success": true,
    "message": "Response generated successfully",
    "data": {
        "message": "Here are the available rooms for next month:\n\n1. Room 101 - Single Bed - $500/month\n2. Room 102 - Double Bed - $800/month\n3. Room 103 - Single Bed - $500/month\n\nWould you like me to help you book any of these rooms?",
        "conversation_id": "conv_1703123456789_abc123def",
        "timestamp": "2024-01-15T10:30:15.000000Z",
        "metadata": {
            "response_time_ms": 1500,
            "model_used": "gpt-4",
            "tokens_used": 150
        }
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Failed to generate response",
    "error": "Rate limit exceeded",
    "data": {
        "conversation_id": "conv_1703123456789_abc123def",
        "timestamp": "2024-01-15T10:30:15.000000Z"
    }
}
```

## Response Field Descriptions

### Required Fields
- **`success`** (boolean): Whether the response was generated successfully
- **`message`** (string): The AI-generated response message to display to the user
- **`data`** (object): Additional response data

### Optional Fields
- **`error`** (string): Error message if success is false
- **`conversation_id`** (string): Echo back the conversation ID for tracking
- **`timestamp`** (string): ISO timestamp of when the response was generated
- **`metadata`** (object): Additional metadata about the response

### Metadata Fields (Optional)
- **`response_time_ms`** (number): Time taken to generate response in milliseconds
- **`model_used`** (string): AI model used for generation
- **`tokens_used`** (number): Number of tokens consumed
- **`confidence_score`** (number): Confidence score of the response (0-1)
- **`suggestions`** (array): Suggested follow-up questions or actions

## Example Responses

### Room Availability Query
```json
{
    "success": true,
    "message": "Here are the available rooms for next month:\n\n**Available Rooms:**\n• Room 101 - Single Bed - $500/month\n• Room 102 - Double Bed - $800/month\n• Room 103 - Single Bed - $500/month\n\n**Next Steps:**\nWould you like me to help you book any of these rooms or provide more details about any specific room?",
    "data": {
        "conversation_id": "conv_1703123456789_abc123def",
        "timestamp": "2024-01-15T10:30:15.000000Z",
        "metadata": {
            "response_time_ms": 1200,
            "model_used": "gpt-4",
            "tokens_used": 120,
            "confidence_score": 0.95,
            "suggestions": [
                "Book Room 101",
                "View room details",
                "Check pricing options"
            ]
        }
    }
}
```

### Payment Inquiry
```json
{
    "success": true,
    "message": "Your current payment status:\n\n**Outstanding Balance:** $500.00\n**Due Date:** January 31, 2024\n**Payment Method:** Credit Card ending in 1234\n\n**Recent Payments:**\n• December 2023: $500.00 (Paid)\n• November 2023: $500.00 (Paid)\n\nWould you like to make a payment now or need help with anything else?",
    "data": {
        "conversation_id": "conv_1703123456789_abc123def",
        "timestamp": "2024-01-15T10:30:15.000000Z",
        "metadata": {
            "response_time_ms": 800,
            "model_used": "gpt-4",
            "tokens_used": 100,
            "confidence_score": 0.98
        }
    }
}
```

### General Inquiry
```json
{
    "success": true,
    "message": "I can help you with various hostel management tasks:\n\n**Available Services:**\n• Room availability and booking\n• Payment status and history\n• Amenity usage and subscriptions\n• Maintenance requests\n• General information\n\nWhat would you like to know about?",
    "data": {
        "conversation_id": "conv_1703123456789_abc123def",
        "timestamp": "2024-01-15T10:30:15.000000Z",
        "metadata": {
            "response_time_ms": 600,
            "model_used": "gpt-4",
            "tokens_used": 80,
            "confidence_score": 0.92,
            "suggestions": [
                "Check room availability",
                "View payment status",
                "Book amenities",
                "Report maintenance issue"
            ]
        }
    }
}
```

### Error Response Example
```json
{
    "success": false,
    "message": "I'm sorry, I couldn't process your request at the moment.",
    "error": "Service temporarily unavailable",
    "data": {
        "conversation_id": "conv_1703123456789_abc123def",
        "timestamp": "2024-01-15T10:30:15.000000Z",
        "metadata": {
            "error_code": "SERVICE_UNAVAILABLE",
            "retry_after": 30
        }
    }
}
```

## Implementation Notes

### User Context
The system sends comprehensive user information including:
- Basic user details (name, email, phone, status)
- Tenant profile information (if user is a tenant)
- Session and conversation tracking
- Timestamp for request tracking

### Conversation Management
- Each conversation has a unique ID for tracking
- The same conversation ID should be echoed back in responses
- Conversation context can be maintained using this ID

### Error Handling
- Always return a valid JSON response
- Include appropriate HTTP status codes
- Provide meaningful error messages
- Consider retry mechanisms for temporary failures

### Response Formatting
- Use Markdown formatting for rich text responses
- Include line breaks for readability
- Use bullet points and formatting for structured information
- Keep responses concise but informative

### Security Considerations
- Validate incoming requests
- Sanitize user input
- Implement rate limiting
- Log requests for monitoring

## Testing

### Test Request
```bash
curl -X POST https://n8n.admin.inventiko.com/webhook-test/crm \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Hello, can you help me?",
    "conversation_id": "test_conv_123",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com",
      "is_tenant": true,
      "status": "active"
    },
    "timestamp": "2024-01-15T10:30:00.000000Z"
  }'
```

### Expected Test Response
```json
{
    "success": true,
    "message": "Hello! I'm your AI assistant for the Hostel CRM system. How can I help you today?",
    "data": {
        "conversation_id": "test_conv_123",
        "timestamp": "2024-01-15T10:30:15.000000Z"
    }
}
```

This documentation provides the complete specification for integrating with the Hostel CRM Chat System via your n8n webhook.
