<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Support\Tenancy\TenantManager;
use App\Models\User;
use App\Models\Project;
use App\Models\BoxType;
use App\Models\ProjectBox;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Message;
use Illuminate\Support\Str;

class MessagesTicketsBoxesSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first() ?? Tenant::create(['name'=>'Default Tenant','domain'=>null]);
        TenantManager::setTenant($tenant);

        $admin = User::where('email','admin@maklon.com')->first() ?? User::first();
        $cs = User::where('email','client@maklon.com')->first() ?? User::first();
        $prod = User::where('email','developer@maklon.com')->first() ?? User::first();

        // Ensure 3 projects
        $projects = collect();
        for ($i=1; $i<=3; $i++) {
            $projects->push(Project::firstOrCreate(
                ['name' => "Demo Project $i"],
                [
                    'description' => 'Seeded project',
                    'status' => 'active',
                    'production_status' => 'draft',
                    'priority' => 'medium',
                    'created_by' => optional($admin)->id,
                ]
            ));
        }

        // Box types (3)
        $boxTypes = collect([
            ['name' => 'Folding Carton','description'=>'Standard folding carton box'],
            ['name' => 'Rigid Box','description'=>'Premium rigid box'],
            ['name' => 'Mailer Box','description'=>'E-commerce mailer box'],
        ])->map(fn($d)=>BoxType::firstOrCreate(['name'=>$d['name']], $d));

        // Project boxes (3)
        for ($i=0;$i<3;$i++) {
            ProjectBox::firstOrCreate([
                'project_id' => $projects[$i % $projects->count()]->id,
                'box_type_id' => $boxTypes[$i % $boxTypes->count()]->id,
            ],[
                'size' => '10x10x5 cm',
                'shape' => 'rectangular',
                'mockup_path' => null,
            ]);
        }

        // Tickets (3) linked to first project
        $project = $projects->first();
        $tickets = collect();
        $statuses = ['open','queued','queued'];
        for ($i=1;$i<=3;$i++) {
            $tickets->push(Ticket::firstOrCreate([
                'project_id' => $project->id,
                'title' => "Seed Ticket $i",
            ],[
                'requested_by' => optional($cs)->id ?? optional($admin)->id,
                'assigned_to' => optional($prod)->id ?? null,
                'priority' => 'normal',
                'status' => $statuses[$i-1] ?? 'queued',
            ]));
        }

        // Ticket messages (3 total)
        for ($i=1;$i<=3;$i++) {
            TicketMessage::firstOrCreate([
                'ticket_id' => $tickets->first()->id,
                'user_id' => optional($cs)->id ?? optional($admin)->id,
                'message' => "Sample ticket message $i",
            ],[
                'attachment_path' => null,
            ]);
        }

        // Generic Messages (3) – one for ticket, one for project, one for ticket
        Message::firstOrCreate([
            'context_type' => Ticket::class,
            'context_id' => $tickets->first()->id,
            'user_id' => optional($admin)->id,
            'body' => 'Hello from Admin in ticket context',
        ]);
        Message::firstOrCreate([
            'context_type' => Project::class,
            'context_id' => $project->id,
            'user_id' => optional($prod)->id ?? optional($admin)->id,
            'body' => 'Message in project context',
        ]);
        Message::firstOrCreate([
            'context_type' => Ticket::class,
            'context_id' => $tickets->last()->id,
            'user_id' => optional($cs)->id ?? optional($admin)->id,
            'body' => 'Another ticket message',
        ]);
    }
}
