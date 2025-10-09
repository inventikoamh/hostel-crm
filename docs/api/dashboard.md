# Dashboard API Module

## Overview
The Dashboard API provides comprehensive analytics, summaries, and insights across all modules of the Hostel CRM system. This module aggregates data from all other modules to provide real-time dashboards, charts, metrics, and performance indicators for administrators and managers.

## Base Endpoints
All dashboard endpoints are prefixed with `/api/v1/dashboard/`

## Endpoints

### 1. Dashboard Overview

#### Get Comprehensive Dashboard Overview
Retrieve a complete dashboard overview with summary statistics, recent activity, quick stats, alerts, charts, and performance metrics.

**GET Version (Testing):**
```
GET /api/v1/dashboard/overview
```

**POST Version (Integration):**
```
POST /api/v1/dashboard/overview
Content-Type: application/json

{
    "date_range": "30_days",
    "include_charts": true,
    "include_alerts": true
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Dashboard overview retrieved successfully",
    "data": {
        "summary": {
            "total_hostels": 2,
            "total_rooms": 15,
            "total_beds": 45,
            "total_tenants": 25,
            "total_users": 30,
            "total_invoices": 120,
            "total_payments": 100,
            "total_enquiries": 50,
            "total_notifications": 200,
            "active_tenants": 20,
            "occupied_beds": 35,
            "pending_enquiries": 5
        },
        "recent_activity": [
            {
                "type": "tenant_registered",
                "message": "New tenant registered: John Doe",
                "timestamp": "2024-01-15T10:30:00.000000Z",
                "data": {"tenant_id": 1}
            },
            {
                "type": "enquiry_received",
                "message": "New enquiry from Jane Smith: Room availability inquiry",
                "timestamp": "2024-01-15T09:15:00.000000Z",
                "data": {"enquiry_id": 1}
            },
            {
                "type": "payment_received",
                "message": "Payment received from John Doe: ₹800",
                "timestamp": "2024-01-15T08:45:00.000000Z",
                "data": {"payment_id": 1}
            }
        ],
        "quick_stats": {
            "today": {
                "new_enquiries": 3,
                "new_tenants": 1,
                "payments_received": 2500,
                "notifications_sent": 15
            },
            "this_month": {
                "new_enquiries": 25,
                "new_tenants": 8,
                "payments_received": 45000,
                "notifications_sent": 150
            },
            "occupancy_rate": 77.78,
            "revenue_growth": 12.5
        },
        "alerts": [
            {
                "type": "warning",
                "title": "Overdue Payments",
                "message": "3 payments are overdue",
                "action": "view_overdue_payments"
            },
            {
                "type": "info",
                "title": "Pending Enquiries",
                "message": "2 enquiries need attention",
                "action": "view_pending_enquiries"
            }
        ],
        "charts": {
            "revenue_chart": [
                {"month": "Jan", "revenue": 15000},
                {"month": "Feb", "revenue": 18000},
                {"month": "Mar", "revenue": 22000}
            ],
            "occupancy_chart": [
                {"month": "Jan", "occupancy_rate": 75.5},
                {"month": "Feb", "occupancy_rate": 78.2},
                {"month": "Mar", "occupancy_rate": 82.1}
            ],
            "enquiry_chart": [
                {"month": "Jan", "enquiries": 15},
                {"month": "Feb", "enquiries": 18},
                {"month": "Mar", "enquiries": 22}
            ],
            "payment_chart": [
                {"month": "Jan", "payments": 12000},
                {"month": "Feb", "payments": 14500},
                {"month": "Mar", "payments": 16800}
            ]
        },
        "performance_metrics": {
            "enquiry_response_rate": 85.5,
            "payment_collection_rate": 92.3,
            "tenant_satisfaction_score": 4.2,
            "system_uptime": 99.9
        }
    }
}
```

### 2. Financial Dashboard

#### Get Financial Dashboard Data
Retrieve comprehensive financial analytics including revenue summaries, payment analytics, invoice analytics, outstanding amounts, monthly trends, and payment method breakdowns.

**GET Version (Testing):**
```
GET /api/v1/dashboard/financial
```

**Response (200):**
```json
{
    "success": true,
    "message": "Financial dashboard data retrieved successfully",
    "data": {
        "revenue_summary": {
            "this_month": 25000,
            "last_month": 22000,
            "growth_rate": 13.64,
            "total_revenue": 150000,
            "average_monthly_revenue": 12500
        },
        "payment_analytics": {
            "total_payments": 100,
            "verified_payments": 95,
            "pending_payments": 3,
            "failed_payments": 2,
            "cancelled_payments": 0,
            "total_amount": 150000,
            "average_payment": 1578.95,
            "payment_methods": {
                "cash": 25,
                "bank_transfer": 40,
                "upi": 30,
                "card": 5
            }
        },
        "invoice_analytics": {
            "total_invoices": 120,
            "paid_invoices": 95,
            "pending_invoices": 20,
            "overdue_invoices": 5,
            "total_amount": 180000,
            "paid_amount": 150000,
            "outstanding_amount": 30000,
            "average_invoice_amount": 1500
        },
        "outstanding_amounts": {
            "total_outstanding": 30000,
            "overdue_amount": 5000,
            "pending_amount": 25000,
            "by_tenant": [
                {
                    "tenant_name": "John Doe",
                    "outstanding_amount": 1500
                },
                {
                    "tenant_name": "Jane Smith",
                    "outstanding_amount": 2000
                }
            ]
        },
        "monthly_trends": [
            {
                "month": "Jan 2024",
                "revenue": 15000,
                "invoices": 25
            },
            {
                "month": "Feb 2024",
                "revenue": 18000,
                "invoices": 30
            },
            {
                "month": "Mar 2024",
                "revenue": 22000,
                "invoices": 35
            }
        ],
        "payment_methods": [
            {
                "method": "bank_transfer",
                "total_amount": 60000,
                "count": 40,
                "percentage": 40
            },
            {
                "method": "upi",
                "total_amount": 45000,
                "count": 30,
                "percentage": 30
            },
            {
                "method": "cash",
                "total_amount": 37500,
                "count": 25,
                "percentage": 25
            },
            {
                "method": "card",
                "total_amount": 7500,
                "count": 5,
                "percentage": 5
            }
        ]
    }
}
```

### 3. Occupancy Dashboard

#### Get Occupancy Dashboard Data
Retrieve comprehensive occupancy analytics including occupancy summaries, hostel occupancy, room occupancy, bed assignments, move-ins/outs, and lease expirations.

**GET Version (Testing):**
```
GET /api/v1/dashboard/occupancy
```

**Response (200):**
```json
{
    "success": true,
    "message": "Occupancy dashboard data retrieved successfully",
    "data": {
        "occupancy_summary": {
            "total_beds": 45,
            "occupied_beds": 35,
            "available_beds": 10,
            "occupancy_rate": 77.78,
            "total_rooms": 15,
            "occupied_rooms": 12
        },
        "hostel_occupancy": [
            {
                "hostel_id": 1,
                "hostel_name": "Sunrise Hostel",
                "total_beds": 25,
                "occupied_beds": 20,
                "occupancy_rate": 80.0
            },
            {
                "hostel_id": 2,
                "hostel_name": "Sunset Hostel",
                "total_beds": 20,
                "occupied_beds": 15,
                "occupancy_rate": 75.0
            }
        ],
        "room_occupancy": [
            {
                "room_id": 1,
                "room_number": "101",
                "hostel_name": "Sunrise Hostel",
                "total_beds": 3,
                "occupied_beds": 3,
                "occupancy_rate": 100.0
            },
            {
                "room_id": 2,
                "room_number": "102",
                "hostel_name": "Sunrise Hostel",
                "total_beds": 3,
                "occupied_beds": 2,
                "occupancy_rate": 66.67
            }
        ],
        "bed_assignments": {
            "total_assignments": 50,
            "active_assignments": 35,
            "completed_assignments": 10,
            "cancelled_assignments": 5,
            "average_duration": 180.5
        },
        "move_ins_outs": {
            "this_month": {
                "move_ins": 5,
                "move_outs": 2
            },
            "last_month": {
                "move_ins": 3,
                "move_outs": 1
            }
        },
        "lease_expirations": {
            "expiring_in_7_days": 2,
            "expiring_in_30_days": 5,
            "expired_leases": 1
        }
    }
}
```

### 4. Tenant Analytics Dashboard

#### Get Tenant Analytics Dashboard Data
Retrieve comprehensive tenant analytics including tenant summaries, status breakdowns, demographics, satisfaction metrics, retention metrics, and communication statistics.

**GET Version (Testing):**
```
GET /api/v1/dashboard/tenants
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant analytics dashboard data retrieved successfully",
    "data": {
        "tenant_summary": {
            "total_tenants": 25,
            "active_tenants": 20,
            "pending_tenants": 3,
            "inactive_tenants": 2,
            "verified_tenants": 22,
            "unverified_tenants": 3
        },
        "tenant_status": {
            "active": 20,
            "pending": 3,
            "inactive": 2
        },
        "tenant_demographics": {
            "age_groups": {
                "18-25": 15,
                "26-35": 8,
                "36-45": 2
            },
            "occupations": {
                "Software Engineer": 8,
                "Student": 12,
                "Business Analyst": 3,
                "Designer": 2
            },
            "companies": {
                "Tech Corp": 5,
                "University": 12,
                "Design Studio": 2,
                "Consulting Firm": 3
            }
        },
        "tenant_satisfaction": {
            "overall_satisfaction": 4.2,
            "response_rate": 75.5,
            "satisfaction_trend": "increasing",
            "common_concerns": [
                "WiFi connectivity",
                "Room maintenance",
                "Noise levels"
            ]
        },
        "tenant_retention": {
            "retention_rate": 85.5,
            "average_tenancy_duration": 180.5,
            "churn_rate": 14.5
        },
        "tenant_communication": {
            "total_notifications": 150,
            "sent_notifications": 140,
            "failed_notifications": 10,
            "average_response_time": 2.5
        }
    }
}
```

### 5. Amenity Usage Dashboard

#### Get Amenity Usage Dashboard Data
Retrieve comprehensive amenity usage analytics including amenity summaries, usage analytics, revenue breakdowns, popular amenities, subscription trends, and usage patterns.

**GET Version (Testing):**
```
GET /api/v1/dashboard/amenities
```

**Response (200):**
```json
{
    "success": true,
    "message": "Amenity usage dashboard data retrieved successfully",
    "data": {
        "amenity_summary": {
            "total_amenities": 10,
            "total_paid_amenities": 5,
            "active_subscriptions": 25,
            "total_usage_records": 500,
            "amenity_revenue": 15000
        },
        "usage_analytics": {
            "daily_usage": 25,
            "monthly_usage": 500,
            "average_usage_per_tenant": 20,
            "peak_usage_hours": ["18:00", "19:00", "20:00"]
        },
        "revenue_breakdown": [
            {
                "amenity_name": "Gym Access",
                "revenue": 5000,
                "subscribers": 15
            },
            {
                "amenity_name": "Laundry Service",
                "revenue": 3000,
                "subscribers": 20
            },
            {
                "amenity_name": "Parking Space",
                "revenue": 2000,
                "subscribers": 10
            }
        ],
        "popular_amenities": [
            {
                "amenity_name": "Laundry Service",
                "subscribers": 20,
                "revenue": 3000
            },
            {
                "amenity_name": "Gym Access",
                "subscribers": 15,
                "revenue": 5000
            },
            {
                "amenity_name": "Parking Space",
                "subscribers": 10,
                "revenue": 2000
            }
        ],
        "subscription_trends": [
            {
                "month": "Jan 2024",
                "new_subscriptions": 5,
                "cancelled_subscriptions": 2
            },
            {
                "month": "Feb 2024",
                "new_subscriptions": 8,
                "cancelled_subscriptions": 1
            },
            {
                "month": "Mar 2024",
                "new_subscriptions": 12,
                "cancelled_subscriptions": 3
            }
        ],
        "usage_patterns": {
            "hourly_usage": [
                {"hour": "06:00", "usage": 5},
                {"hour": "07:00", "usage": 8},
                {"hour": "18:00", "usage": 25},
                {"hour": "19:00", "usage": 30}
            ],
            "daily_usage": [
                {"day": "Monday", "usage": 120},
                {"day": "Tuesday", "usage": 135},
                {"day": "Wednesday", "usage": 140}
            ],
            "weekly_usage": [
                {"week": "Week 1", "usage": 500},
                {"week": "Week 2", "usage": 520},
                {"week": "Week 3", "usage": 480}
            ]
        }
    }
}
```

### 6. Enquiry Analytics Dashboard

#### Get Enquiry Analytics Dashboard Data
Retrieve comprehensive enquiry analytics including enquiry summaries, conversion analytics, source analytics, response analytics, priority breakdowns, and trend analysis.

**GET Version (Testing):**
```
GET /api/v1/dashboard/enquiries
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry analytics dashboard data retrieved successfully",
    "data": {
        "enquiry_summary": {
            "total_enquiries": 50,
            "new_enquiries": 5,
            "in_progress_enquiries": 8,
            "resolved_enquiries": 30,
            "closed_enquiries": 7,
            "overdue_enquiries": 2
        },
        "conversion_analytics": {
            "conversion_rate": 60.0,
            "total_conversions": 30,
            "average_conversion_time": 24.5
        },
        "source_analytics": {
            "website": 25,
            "phone": 15,
            "walk-in": 8,
            "referral": 2
        },
        "response_analytics": {
            "response_rate": 85.5,
            "average_response_time": 12.5,
            "response_time_trend": "decreasing"
        },
        "priority_breakdown": {
            "high": 10,
            "medium": 25,
            "low": 15
        },
        "trend_analysis": [
            {
                "month": "Jan 2024",
                "total_enquiries": 15,
                "resolved_enquiries": 12
            },
            {
                "month": "Feb 2024",
                "total_enquiries": 18,
                "resolved_enquiries": 15
            },
            {
                "month": "Mar 2024",
                "total_enquiries": 22,
                "resolved_enquiries": 18
            }
        ]
    }
}
```

### 7. Notification Analytics Dashboard

#### Get Notification Analytics Dashboard Data
Retrieve comprehensive notification analytics including notification summaries, delivery analytics, type breakdowns, success rates, retry analytics, and trend analysis.

**GET Version (Testing):**
```
GET /api/v1/dashboard/notifications
```

**Response (200):**
```json
{
    "success": true,
    "message": "Notification analytics dashboard data retrieved successfully",
    "data": {
        "notification_summary": {
            "total_notifications": 200,
            "pending_notifications": 10,
            "sent_notifications": 180,
            "failed_notifications": 8,
            "cancelled_notifications": 2,
            "scheduled_notifications": 5
        },
        "delivery_analytics": {
            "delivery_rate": 90.0,
            "average_delivery_time": 5.5,
            "retry_rate": 15.0
        },
        "type_breakdown": {
            "tenant_added": 25,
            "tenant_updated": 15,
            "enquiry_added": 30,
            "invoice_created": 40,
            "payment_received": 35,
            "lease_expiring": 10
        },
        "success_rates": {
            "tenant_added": 95.0,
            "tenant_updated": 90.0,
            "enquiry_added": 85.0,
            "invoice_created": 92.0,
            "payment_received": 88.0,
            "lease_expiring": 100.0
        },
        "retry_analytics": {
            "total_retries": 45,
            "average_retries": 1.2,
            "max_retries_reached": 5,
            "retryable_notifications": 3
        },
        "trend_analysis": [
            {
                "month": "Jan 2024",
                "total_notifications": 50,
                "sent_notifications": 45
            },
            {
                "month": "Feb 2024",
                "total_notifications": 60,
                "sent_notifications": 55
            },
            {
                "month": "Mar 2024",
                "total_notifications": 70,
                "sent_notifications": 65
            }
        ]
    }
}
```

### 8. System Health Dashboard

#### Get System Health Dashboard Data
Retrieve comprehensive system health analytics including system summaries, user activity, database stats, performance metrics, error logs, and maintenance alerts.

**GET Version (Testing):**
```
GET /api/v1/dashboard/system-health
```

**Response (200):**
```json
{
    "success": true,
    "message": "System health dashboard data retrieved successfully",
    "data": {
        "system_summary": {
            "total_users": 30,
            "active_users": 25,
            "system_users": 5,
            "tenant_users": 25,
            "super_admins": 1,
            "last_login": "2024-01-15T10:30:00.000000Z"
        },
        "user_activity": {
            "today_logins": 15,
            "this_week_logins": 45,
            "active_sessions": 8,
            "user_growth": 12.5
        },
        "database_stats": {
            "total_records": 1500,
            "database_size": "25.5 MB",
            "table_counts": {
                "users": 30,
                "tenant_profiles": 25,
                "invoices": 120,
                "payments": 100
            },
            "index_usage": {
                "primary_keys": 100,
                "foreign_keys": 200,
                "custom_indexes": 50
            }
        },
        "performance_metrics": {
            "average_response_time": 150.5,
            "memory_usage": 75.2,
            "cpu_usage": 45.8,
            "disk_usage": 60.3
        },
        "error_logs": {
            "total_errors": 25,
            "errors_today": 2,
            "error_types": {
                "database": 10,
                "validation": 8,
                "authentication": 5,
                "other": 2
            },
            "critical_errors": 1
        },
        "maintenance_alerts": {
            "scheduled_maintenance": [
                {
                    "date": "2024-01-20",
                    "description": "Database optimization",
                    "duration": "2 hours"
                }
            ],
            "system_updates": [
                {
                    "version": "v1.2.0",
                    "description": "Security patches",
                    "status": "pending"
                }
            ],
            "backup_status": {
                "last_backup": "2024-01-15T02:00:00.000000Z",
                "status": "success",
                "size": "25.5 MB"
            },
            "security_alerts": [
                {
                    "type": "failed_login",
                    "count": 3,
                    "severity": "medium"
                }
            ]
        }
    }
}
```

### 9. Dashboard Widgets

#### Get Dashboard Widgets
Retrieve available dashboard widgets, user-specific widgets, and widget data for customizable dashboard layouts.

**GET Version (Testing):**
```
GET /api/v1/dashboard/widgets
```

**POST Version (Integration):**
```
POST /api/v1/dashboard/widgets
Content-Type: application/json

{
    "widgets": ["revenue_chart", "occupancy_gauge", "recent_activity", "quick_stats", "alerts"]
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Dashboard widgets retrieved successfully",
    "data": {
        "available_widgets": [
            {
                "revenue_chart": {
                    "name": "Revenue Chart",
                    "type": "chart"
                },
                "occupancy_gauge": {
                    "name": "Occupancy Gauge",
                    "type": "gauge"
                },
                "recent_activity": {
                    "name": "Recent Activity",
                    "type": "list"
                },
                "quick_stats": {
                    "name": "Quick Stats",
                    "type": "stats"
                },
                "alerts": {
                    "name": "Alerts",
                    "type": "alerts"
                },
                "enquiry_trend": {
                    "name": "Enquiry Trend",
                    "type": "chart"
                },
                "payment_summary": {
                    "name": "Payment Summary",
                    "type": "summary"
                },
                "tenant_satisfaction": {
                    "name": "Tenant Satisfaction",
                    "type": "score"
                }
            }
        ],
        "user_widgets": [
            "revenue_chart",
            "occupancy_gauge",
            "recent_activity",
            "quick_stats",
            "alerts"
        ],
        "widget_data": {
            "revenue_chart": [
                {"month": "Jan", "revenue": 15000},
                {"month": "Feb", "revenue": 18000},
                {"month": "Mar", "revenue": 22000}
            ],
            "occupancy_gauge": 77.78,
            "recent_activity": [
                {
                    "type": "tenant_registered",
                    "message": "New tenant registered: John Doe",
                    "timestamp": "2024-01-15T10:30:00.000000Z"
                }
            ],
            "quick_stats": {
                "today": {
                    "new_enquiries": 3,
                    "new_tenants": 1,
                    "payments_received": 2500,
                    "notifications_sent": 15
                }
            },
            "alerts": [
                {
                    "type": "warning",
                    "title": "Overdue Payments",
                    "message": "3 payments are overdue"
                }
            ]
        }
    }
}
```

## Data Models

### Dashboard Overview Object
```json
{
    "summary": {
        "total_hostels": 2,
        "total_rooms": 15,
        "total_beds": 45,
        "total_tenants": 25,
        "total_users": 30,
        "total_invoices": 120,
        "total_payments": 100,
        "total_enquiries": 50,
        "total_notifications": 200,
        "active_tenants": 20,
        "occupied_beds": 35,
        "pending_enquiries": 5
    },
    "recent_activity": [
        {
            "type": "tenant_registered",
            "message": "New tenant registered: John Doe",
            "timestamp": "2024-01-15T10:30:00.000000Z",
            "data": {"tenant_id": 1}
        }
    ],
    "quick_stats": {
        "today": {
            "new_enquiries": 3,
            "new_tenants": 1,
            "payments_received": 2500,
            "notifications_sent": 15
        },
        "this_month": {
            "new_enquiries": 25,
            "new_tenants": 8,
            "payments_received": 45000,
            "notifications_sent": 150
        },
        "occupancy_rate": 77.78,
        "revenue_growth": 12.5
    },
    "alerts": [
        {
            "type": "warning",
            "title": "Overdue Payments",
            "message": "3 payments are overdue",
            "action": "view_overdue_payments"
        }
    ],
    "charts": {
        "revenue_chart": [
            {"month": "Jan", "revenue": 15000},
            {"month": "Feb", "revenue": 18000}
        ],
        "occupancy_chart": [
            {"month": "Jan", "occupancy_rate": 75.5},
            {"month": "Feb", "occupancy_rate": 78.2}
        ]
    },
    "performance_metrics": {
        "enquiry_response_rate": 85.5,
        "payment_collection_rate": 92.3,
        "tenant_satisfaction_score": 4.2,
        "system_uptime": 99.9
    }
}
```

### Financial Dashboard Object
```json
{
    "revenue_summary": {
        "this_month": 25000,
        "last_month": 22000,
        "growth_rate": 13.64,
        "total_revenue": 150000,
        "average_monthly_revenue": 12500
    },
    "payment_analytics": {
        "total_payments": 100,
        "verified_payments": 95,
        "pending_payments": 3,
        "failed_payments": 2,
        "cancelled_payments": 0,
        "total_amount": 150000,
        "average_payment": 1578.95,
        "payment_methods": {
            "cash": 25,
            "bank_transfer": 40,
            "upi": 30,
            "card": 5
        }
    },
    "invoice_analytics": {
        "total_invoices": 120,
        "paid_invoices": 95,
        "pending_invoices": 20,
        "overdue_invoices": 5,
        "total_amount": 180000,
        "paid_amount": 150000,
        "outstanding_amount": 30000,
        "average_invoice_amount": 1500
    },
    "outstanding_amounts": {
        "total_outstanding": 30000,
        "overdue_amount": 5000,
        "pending_amount": 25000,
        "by_tenant": [
            {
                "tenant_name": "John Doe",
                "outstanding_amount": 1500
            }
        ]
    },
    "monthly_trends": [
        {
            "month": "Jan 2024",
            "revenue": 15000,
            "invoices": 25
        }
    ],
    "payment_methods": [
        {
            "method": "bank_transfer",
            "total_amount": 60000,
            "count": 40,
            "percentage": 40
        }
    ]
}
```

## Error Handling

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "date_range": ["The date range field is required."],
        "include_charts": ["The include charts field must be true or false."]
    }
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve dashboard overview",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: All dashboard endpoints are public (read-only operations)
- **Authentication Method**: Bearer token via Laravel Sanctum (optional for enhanced features)

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/dashboard/overview
http://localhost/api/v1/dashboard/financial
http://localhost/api/v1/dashboard/occupancy
http://localhost/api/v1/dashboard/tenants
http://localhost/api/v1/dashboard/amenities
http://localhost/api/v1/dashboard/enquiries
http://localhost/api/v1/dashboard/notifications
http://localhost/api/v1/dashboard/system-health
http://localhost/api/v1/dashboard/widgets
```

### cURL Examples
```bash
# Get dashboard overview
curl -X GET http://localhost/api/v1/dashboard/overview

# Get financial dashboard
curl -X GET http://localhost/api/v1/dashboard/financial

# Get occupancy dashboard
curl -X GET http://localhost/api/v1/dashboard/occupancy

# Get tenant analytics
curl -X GET http://localhost/api/v1/dashboard/tenants

# Get amenity usage dashboard
curl -X GET http://localhost/api/v1/dashboard/amenities

# Get enquiry analytics
curl -X GET http://localhost/api/v1/dashboard/enquiries

# Get notification analytics
curl -X GET http://localhost/api/v1/dashboard/notifications

# Get system health
curl -X GET http://localhost/api/v1/dashboard/system-health

# Get dashboard widgets
curl -X POST http://localhost/api/v1/dashboard/widgets \
  -H "Content-Type: application/json" \
  -d '{"widgets": ["revenue_chart", "occupancy_gauge", "recent_activity"]}'
```

## Business Rules

1. **Data Aggregation**: All dashboard data is aggregated from existing modules
2. **Real-time Updates**: Dashboard data reflects current system state
3. **Performance Optimization**: Complex calculations are cached for performance
4. **Data Privacy**: Sensitive data is filtered based on user permissions
5. **Chart Data**: Chart data is provided in formats suitable for visualization libraries
6. **Alert System**: Alerts are generated based on business rules and thresholds
7. **Trend Analysis**: Trends are calculated over configurable time periods
8. **Widget Customization**: Users can customize their dashboard layout
9. **Export Capabilities**: Dashboard data can be exported for reporting
10. **Historical Data**: Dashboard maintains historical data for trend analysis
11. **Performance Metrics**: System performance metrics are monitored and reported
12. **Error Tracking**: System errors are tracked and reported in health dashboard
13. **Maintenance Alerts**: Scheduled maintenance and updates are tracked
14. **Security Monitoring**: Security events are monitored and reported
15. **Backup Status**: Backup status is monitored and reported

## Related Modules

- **Authentication API**: User authentication for enhanced features
- **Hostels API**: Hostel data for occupancy analytics
- **Tenants API**: Tenant data for tenant analytics
- **Rooms & Beds API**: Room and bed data for occupancy analytics
- **Invoices API**: Invoice data for financial analytics
- **Payments API**: Payment data for financial analytics
- **Amenities API**: Amenity data for usage analytics
- **Users API**: User data for system health analytics
- **Enquiries API**: Enquiry data for enquiry analytics
- **Notifications API**: Notification data for notification analytics

---

*Module: Dashboard API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
