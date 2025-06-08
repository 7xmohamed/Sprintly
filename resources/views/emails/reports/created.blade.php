<!DOCTYPE html>
<html>
<head>
    <title>New Report Created</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { background: #f8fafc; padding: 20px; border-radius: 0 0 5px 5px; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .details { margin: 20px 0; padding: 15px; background: white; border-radius: 5px; }
        .report-content { background: #f1f5f9; padding: 15px; border-left: 4px solid #059669; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Daily Report Created</h1>
        </div>
        <div class="content">
            <p>Hello Supervisor,</p>
            
            <p>A new daily report has been created:</p>
            
            <div class="details">
                <div class="report-content">
                    <p><strong>Summary:</strong></p>
                    <p>{{ $report->summary }}</p>
                </div>

                <p><strong>Created By:</strong> {{ $report->user->first_name }} {{ $report->user->last_name }}</p>
                <p><strong>Project:</strong> {{ $report->project->name }}</p>
                <p><strong>Date:</strong> {{ $report->date->format('F j, Y') }}</p>
                <p><strong>Submitted At:</strong> {{ $report->submitted_at->format('F j, Y H:i') }}</p>
            </div>

            <p>
                <a href="{{ url('/reports') }}" style="background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    View Reports
                </a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated message from your Enterprise Management System.</p>
        </div>
    </div>
</body>
</html> 