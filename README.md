# time-manager

Time Manager is an application I developed to track my daily activities.
- When you switch activities, the app records how long the previous activity lasted.
- The data collected is used solely for analytics in other projects and not for any external purposes.

You can get the data stored in the database using the api
GET: /api/time-manager?token=xxx
Response:
[
    {
        "id": "3",
        "activity_name": "Break",
        "start_time": "2025-09-05 11:11:54",
        "end_time": "2025-09-05 11:12:00",
        "duration_seconds": "6"
    },
    ...
]
