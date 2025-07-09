<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Models\PodcastTeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PodcastTeamController extends Controller
{
    /**
     * Show team management page for podcast owner
     */
    public function manage(Podcast $podcast)
    {
        // Check if user is the podcast owner
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403, 'Only the podcast owner can manage team members.');
        }

        $podcast->load([
            'teamMembers.user',
            'activeTeamMembers.user',
            'pendingTeamMembers.user'
        ]);

        return view('podcasts.team.manage', compact('podcast'));
    }

    /**
     * Invite a user to join the podcast team
     */
    public function invite(Request $request, Podcast $podcast)
    {
        // Check if user is the podcast owner
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403, 'Only the podcast owner can invite team members.');
        }

        $request->validate([
            'user_identifier' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['member', 'moderator'])],
            'can_add_games' => 'boolean',
            'can_delete_games' => 'boolean',
            'can_post_reviews' => 'boolean',
            'can_manage_episodes' => 'boolean',
        ]);

        // Find user by email or username
        $user = User::where('email', $request->user_identifier)
                   ->orWhere('name', $request->user_identifier)
                   ->first();

        if (!$user) {
            return back()->withErrors([
                'user_identifier' => 'User not found. Please check the email or username.'
            ]);
        }

        // Check if user is already a team member or has pending invitation
        $existingMember = $podcast->teamMembers()->where('user_id', $user->id)->first();
        
        if ($existingMember) {
            if ($existingMember->isAccepted()) {
                return back()->withErrors([
                    'user_identifier' => 'This user is already a team member.'
                ]);
            } else {
                return back()->withErrors([
                    'user_identifier' => 'This user already has a pending invitation.'
                ]);
            }
        }

        // Check if trying to invite the owner
        if ($user->id === $podcast->owner_id) {
            return back()->withErrors([
                'user_identifier' => 'Cannot invite the podcast owner as a team member.'
            ]);
        }

        // Create the invitation
        $podcast->teamMembers()->create([
            'user_id' => $user->id,
            'role' => $request->role,
            'invited_by_owner' => true,
            'can_add_games' => $request->boolean('can_add_games', true),
            'can_delete_games' => $request->boolean('can_delete_games', false),
            'can_post_reviews' => $request->boolean('can_post_reviews', true),
            'can_manage_episodes' => $request->boolean('can_manage_episodes', false),
            'invited_at' => now(),
            // accepted_at remains null for pending invitations
        ]);

        return back()->with('success', "Invitation sent to {$user->name} successfully!");
    }

    /**
     * Accept a team invitation
     */
    public function acceptInvitation(Podcast $podcast, PodcastTeamMember $teamMember)
    {
        if (!Auth::check() || Auth::id() !== $teamMember->user_id) {
            abort(403, 'You can only accept your own invitations.');
        }

        if ($teamMember->isAccepted()) {
            return back()->with('error', 'This invitation has already been accepted.');
        }

        $teamMember->update([
            'accepted_at' => now(),
        ]);

        return redirect()->route('podcasts.dashboard')
            ->with('success', "You are now a team member of {$podcast->name}!");
    }

    /**
     * Decline a team invitation
     */
    public function declineInvitation(Podcast $podcast, PodcastTeamMember $teamMember)
    {
        if (!Auth::check() || Auth::id() !== $teamMember->user_id) {
            abort(403, 'You can only decline your own invitations.');
        }

        if ($teamMember->isAccepted()) {
            return back()->with('error', 'Cannot decline an already accepted invitation.');
        }

        $teamMember->delete();

        return back()->with('success', 'Invitation declined.');
    }

    /**
     * Remove a team member (owner only)
     */
    public function removeMember(Podcast $podcast, PodcastTeamMember $teamMember)
    {
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403, 'Only the podcast owner can remove team members.');
        }

        $memberName = $teamMember->user->name;
        $teamMember->delete();

        return back()->with('success', "Removed {$memberName} from the team.");
    }

    /**
     * Update team member permissions (owner only)
     */
    public function updatePermissions(Request $request, Podcast $podcast, PodcastTeamMember $teamMember)
    {
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403, 'Only the podcast owner can update permissions.');
        }

        $request->validate([
            'role' => ['required', 'string', Rule::in(['member', 'moderator'])],
            'can_add_games' => 'boolean',
            'can_delete_games' => 'boolean',
            'can_post_reviews' => 'boolean',
            'can_manage_episodes' => 'boolean',
        ]);

        $teamMember->update([
            'role' => $request->role,
            'can_add_games' => $request->boolean('can_add_games'),
            'can_delete_games' => $request->boolean('can_delete_games'),
            'can_post_reviews' => $request->boolean('can_post_reviews'),
            'can_manage_episodes' => $request->boolean('can_manage_episodes'),
        ]);

        return back()->with('success', 'Team member permissions updated successfully!');
    }

    /**
     * Leave a team (team member self-removal)
     */
    public function leaveTeam(Podcast $podcast)
    {
        if (!Auth::check()) {
            abort(403, 'You must be logged in to leave a team.');
        }

        $teamMember = $podcast->teamMembers()->where('user_id', Auth::id())->first();

        if (!$teamMember) {
            return back()->with('error', 'You are not a member of this podcast team.');
        }

        if ($teamMember->user_id === $podcast->owner_id) {
            return back()->with('error', 'Podcast owners cannot leave their own team.');
        }

        $teamMember->delete();

        return redirect()->route('podcasts.dashboard')
            ->with('success', "You have left the {$podcast->name} team.");
    }

    /**
     * Show pending invitations for the authenticated user
     */
    public function myInvitations()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $invitations = PodcastTeamMember::where('user_id', Auth::id())
            ->whereNull('accepted_at')
            ->with('podcast')
            ->latest('invited_at')
            ->get();

        return view('podcasts.team.invitations', compact('invitations'));
    }

    /**
     * Check if user can perform specific action on podcast
     */
    public function canPerformAction(Podcast $podcast, string $action, ?User $user = null): bool
    {
        $user = $user ?: Auth::user();
        
        if (!$user) {
            return false;
        }

        // Owner can do everything
        if ($podcast->owner_id === $user->id) {
            return true;
        }

        // Check team member permissions
        $teamMember = $podcast->activeTeamMembers()
            ->where('user_id', $user->id)
            ->first();

        if (!$teamMember) {
            return false;
        }

        // Check specific permissions
        return match($action) {
            'post_reviews' => $teamMember->can_post_reviews,
            'add_games' => $teamMember->can_add_games,
            'delete_games' => $teamMember->can_delete_games,
            'manage_episodes' => $teamMember->can_manage_episodes,
            'manage_team' => false, // Only owner can manage team
            'sync_rss' => $teamMember->role === 'moderator' || $teamMember->can_manage_episodes,
            default => false,
        };
    }

    /**
     * Get user's available podcasts for posting reviews
     */
    public function getAvailablePodcastsForUser(?User $user = null): \Illuminate\Database\Eloquent\Collection
    {
        $user = $user ?: Auth::user();
        
        if (!$user) {
            return collect();
        }

        // Get owned podcasts
        $ownedPodcasts = $user->podcasts()->approved()->get();

        // Get team member podcasts where user can post reviews
        $teamPodcasts = Podcast::approved()
            ->whereHas('activeTeamMembers', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('can_post_reviews', true);
            })
            ->get();

        return $ownedPodcasts->merge($teamPodcasts)->unique('id');
    }
} 