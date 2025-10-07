# Availability Module

## Overview

The Availability Module provides comprehensive room and bed availability checking functionality based on lease dates. This module allows administrators to check which rooms and beds are available for specific lease periods, taking into account existing reservations and assignments.

## Features

### Core Functionality
- **Date-based Availability Checking**: Check availability for specific lease start and end dates
- **Comprehensive Bed Status**: Track available, occupied, reserved, and maintenance beds
- **Assignment History**: View current and historical bed assignments
- **Date Overlap Detection**: Smart detection of conflicting reservations
- **Real-time Results**: AJAX-powered availability checking with instant results

### Key Capabilities
- **Multi-hostel Support**: Check availability across different hostels
- **Flexible Date Ranges**: Support for start date only or full date range checking
- **Detailed Reporting**: Comprehensive availability reports with statistics
- **Visual Interface**: User-friendly interface with color-coded status indicators
- **Responsive Design**: Works on all devices and screen sizes

## Architecture

### Controller
- **`AvailabilityController`**: Handles availability checking logic and data processing

### Models Used
- **`Hostel`**: Hostel information and relationships
- **`Room`**: Room details and bed relationships
- **`Bed`**: Individual bed information and status
- **`BedAssignment`**: Bed assignment history and current status

### Views
- **`availability/index.blade.php`**: Main availability checking interface

## Routes

### Web Routes
```php
// Display availability search form
Route::get('/availability', [AvailabilityController::class, 'index'])
    ->name('availability.index')
    ->middleware('auth');

// Check availability for specified dates
Route::post('/availability/check', [AvailabilityController::class, 'check'])
    ->name('availability.check')
    ->middleware('auth');
```

## Controller Methods

### `index()`
**Purpose**: Display the availability search form.

**Returns**: View with hostel selection and date input forms.

**Features**:
- Hostel dropdown populated with active hostels
- Date input fields for lease start and end dates
- Responsive form layout

### `check(Request $request)`
**Purpose**: Process availability checking request and return comprehensive results.

**Parameters**:
- `hostel_id` (required): ID of the hostel to check
- `lease_start_date` (required): Start date of the lease period
- `lease_end_date` (optional): End date of the lease period

**Validation**:
```php
$request->validate([
    'hostel_id' => 'required|exists:hostels,id',
    'lease_start_date' => 'required|date',
    'lease_end_date' => 'nullable|date|after:lease_start_date',
]);
```

**Returns**: JSON response with comprehensive availability data.

**Response Structure**:
```json
{
    "hostel": {
        "id": 1,
        "name": "Sunrise Hostel",
        "address": "123 Main Street"
    },
    "search_criteria": {
        "lease_start_date": "Oct 1, 2025",
        "lease_end_date": "Oct 31, 2025"
    },
    "summary": {
        "total_rooms": 8,
        "total_beds": 32,
        "available_beds": 15,
        "occupied_beds": 12,
        "reserved_beds": 3,
        "maintenance_beds": 2
    },
    "rooms": [
        {
            "room_id": 1,
            "room_number": "G1",
            "room_name": "Ground Floor Room 1",
            "floor": 0,
            "room_type": "Standard",
            "total_beds": 4,
            "available_beds": 2,
            "occupied_beds": 1,
            "reserved_beds": 1,
            "maintenance_beds": 0,
            "beds": [
                {
                    "bed_id": 1,
                    "bed_number": "01",
                    "bed_type": "Single",
                    "monthly_rent": 5000,
                    "status": "available",
                    "availability": "available",
                    "availability_reason": "Bed is available for assignment",
                    "current_assignments": []
                }
            ]
        }
    ]
}
```

### `checkBedAvailability($bed, $leaseStartDate, $leaseEndDate = null)`
**Purpose**: Private method to determine if a specific bed is available for the given lease period.

**Parameters**:
- `$bed`: Bed model instance
- `$leaseStartDate`: Carbon instance of lease start date
- `$leaseEndDate`: Carbon instance of lease end date (optional)

**Returns**: Array with availability status and reason.

**Logic**:
1. **Maintenance Beds**: Always unavailable
2. **Available Beds**: Always available
3. **Assignment Conflicts**: Check for date overlaps with existing assignments
4. **Reserved Beds**: Check if reservation period conflicts with requested period

**Availability Statuses**:
- `available`: Bed is available for assignment
- `occupied`: Bed is currently occupied
- `reserved`: Bed is reserved for future assignment
- `maintenance`: Bed is under maintenance

## User Interface

### Search Form
- **Hostel Selection**: Dropdown with all active hostels
- **Lease Start Date**: Required date input
- **Lease End Date**: Optional date input
- **Search Button**: Triggers availability check

### Results Display
- **Summary Statistics**: Overview of total and available beds
- **Room Details**: Individual room information with bed breakdowns
- **Bed Information**: Detailed bed status and assignment information
- **Visual Indicators**: Color-coded status badges and cards

### Status Indicators
- **Available**: Green color scheme
- **Occupied**: Red color scheme
- **Reserved**: Purple color scheme
- **Maintenance**: Yellow color scheme

## Availability Logic

### Date Overlap Detection
The system uses sophisticated date overlap detection to determine bed availability:

```php
// Check for overlap between assignment period and requested period
$hasOverlap = $assignmentStart->lt($leaseEndDate) && $assignmentEnd->gt($leaseStartDate);
```

### Assignment Status Handling
- **Active Assignments**: Beds are marked as occupied
- **Reserved Assignments**: Beds are marked as reserved
- **Inactive Assignments**: Ignored for availability checking

### Edge Cases
- **No End Date**: If lease end date is not provided, only check against lease start date
- **Assignment Without End Date**: Treat as ongoing assignment
- **Maintenance Status**: Always unavailable regardless of assignments

## Integration

### Sidebar Navigation
The availability page is accessible through the main sidebar navigation:
- **Icon**: Search icon (`fas fa-search`)
- **Label**: "Availability"
- **Position**: After "Floor Map" in the navigation

### Related Modules
- **Room Module**: Provides room and bed data
- **Tenant Module**: Provides assignment information
- **Map Module**: Visual representation of bed status
- **Hostel Module**: Hostel selection and information

## Error Handling

### Validation Errors
- **Missing Hostel**: Required field validation
- **Invalid Dates**: Date format and logic validation
- **Date Logic**: End date must be after start date

### System Errors
- **Database Errors**: Graceful handling of database connection issues
- **Missing Data**: Proper handling of missing relationships
- **API Errors**: User-friendly error messages for AJAX requests

## Performance Considerations

### Database Optimization
- **Eager Loading**: Load relationships to prevent N+1 queries
- **Efficient Queries**: Optimized queries for large datasets
- **Indexing**: Proper database indexing for fast lookups

### Frontend Optimization
- **AJAX Requests**: Asynchronous availability checking
- **Loading States**: User feedback during processing
- **Error Handling**: Graceful error handling and user feedback

## Security

### Authentication
- **Middleware**: All routes require authentication
- **CSRF Protection**: Form submissions include CSRF tokens
- **Input Validation**: Comprehensive input validation and sanitization

### Data Access
- **Hostel Access**: Users can only check availability for accessible hostels
- **Data Filtering**: Results filtered based on user permissions
- **Secure Responses**: No sensitive data exposed in responses

## Testing

### Unit Tests
- **Controller Methods**: Test availability checking logic
- **Date Logic**: Test date overlap detection
- **Edge Cases**: Test various date scenarios

### Integration Tests
- **API Endpoints**: Test availability checking endpoints
- **Database Integration**: Test with real database data
- **User Interface**: Test form submission and results display

### Test Scenarios
- **Available Beds**: Test beds with no assignments
- **Occupied Beds**: Test beds with active assignments
- **Reserved Beds**: Test beds with future reservations
- **Date Overlaps**: Test various date overlap scenarios
- **Edge Cases**: Test boundary conditions and edge cases

## Future Enhancements

### Planned Features
- **Bulk Availability Checking**: Check multiple hostels simultaneously
- **Availability Calendar**: Visual calendar view of bed availability
- **Booking Integration**: Direct booking from availability results
- **Notification System**: Alerts for availability changes
- **Reporting**: Advanced availability reports and analytics

### API Enhancements
- **RESTful API**: Full API for availability checking
- **Webhook Support**: Real-time availability updates
- **Third-party Integration**: Integration with external booking systems

### User Experience
- **Advanced Filters**: Filter by room type, price range, amenities
- **Saved Searches**: Save and reuse common availability searches
- **Export Functionality**: Export availability results to various formats
- **Mobile App**: Dedicated mobile application for availability checking

## Troubleshooting

### Common Issues

#### No Results Returned
1. Check if hostel has rooms and beds
2. Verify date format and logic
3. Check for database connection issues
4. Verify user permissions

#### Incorrect Availability Status
1. Check bed assignment data
2. Verify date overlap logic
3. Check for timezone issues
4. Review assignment status values

#### Performance Issues
1. Check database indexing
2. Review query optimization
3. Monitor server resources
4. Check for N+1 query problems

### Debug Information
- **Console Logs**: Check browser console for JavaScript errors
- **Server Logs**: Check Laravel logs for server-side errors
- **Database Queries**: Use Laravel Debugbar to monitor queries
- **Network Tab**: Check AJAX request/response in browser dev tools

## Maintenance

### Regular Tasks
- **Database Cleanup**: Remove old inactive assignments
- **Performance Monitoring**: Monitor query performance
- **Error Log Review**: Regular review of error logs
- **User Feedback**: Collect and address user feedback

### Updates
- **Feature Updates**: Regular feature additions and improvements
- **Security Updates**: Keep dependencies updated
- **Performance Optimization**: Continuous performance improvements
- **Bug Fixes**: Address reported issues promptly
