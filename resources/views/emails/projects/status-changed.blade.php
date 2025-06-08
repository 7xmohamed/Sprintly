<!DOCTYPE html>
<html>
<head>
    <title>Project Status Updated</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0891b2; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { background: #f8fafc; padding: 20px; border-radius: 0 0 5px 5px; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .details { margin: 20px 0; padding: 15px; background: white; border-radius: 5px; }
        .status-change { background: #e5e7eb; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Project Status Updated</h1>
        </div>
        <div class="content">
            <p>Hello Director,</p>
            
            <p>A project's status has been updated:</p>
            
            <div class="details">
                <h2>{{ $project->name }}</h2>
                <p><strong>Description:</strong> {{ $project->description }}</p>
                
                <div class="status-change">
                    <p><strong>Status Changed:</strong></p>
                    <p>From: {{ ucfirst($oldStatus) }}</p>
                    <p>To: {{ ucfirst($newStatus) }}</p>
                </div>

                <p><strong>Team Leader:</strong> 
                    @php
                        $teamLeader = $project->members()->where('project_members.role', 'team_leader')->first();
                    @endphp
                    {{ $teamLeader ? $teamLeader->name : 'Not assigned' }}
                </p>
                <p><strong>Start Date:</strong> {{ $project->start_date ? $project->start_date->format('F j, Y') : 'Not set' }}</p>
                <p><strong>Due Date:</strong> {{ $project->end_date ? $project->end_date->format('F j, Y') : 'Not set' }}</p>
            </div>

            <p>
                <a href="{{ url('/projects/' . $project->id) }}" style="background: #0891b2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    View Project Details
                </a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated message from your Enterprise Management System.</p>
        </div>
    </div>
</body>
</html> 