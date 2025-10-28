<h2>Weekly Summary</h2>

<p>Here is your weekly summary for projects and time tracking.</p>

<ul>
    <li><strong>Period:</strong> {{ $summary['period'] ?? '' }}</li>
    <li><strong>Total Hours Logged:</strong> {{ $summary['total_hours'] ?? 0 }}</li>
    <li><strong>Billable Hours:</strong> {{ $summary['billable_hours'] ?? 0 }}</li>
    <li><strong>On-time Completion Rate:</strong> {{ $summary['on_time_rate'] ?? 0 }}%</li>
    <li><strong>Active High Risks:</strong> {{ $summary['high_risks'] ?? 0 }}</li>
    <li><strong>Avg Team Utilization:</strong> {{ $summary['avg_utilization'] ?? 0 }}%</li>
    <li><strong>Top Variance Project:</strong> {{ $summary['top_variance'][0]['project'] ?? 'N/A' }} ({{ $summary['top_variance'][0]['currency'] ?? '' }} {{ isset($summary['top_variance'][0]['variance']) ? number_format($summary['top_variance'][0]['variance'], 2) : '' }})
    </li>
    <li><strong>Note:</strong> Detailed CSVs attached where applicable.</li>
    </ul>

<p>You can opt out from weekly emails in your profile settings if available.</p>

