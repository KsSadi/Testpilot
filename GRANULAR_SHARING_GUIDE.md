# Granular Sharing System - Complete Guide

## Overview
The sharing system now supports **three levels of granular access**:
- **Project Level**: Share entire project with all modules and test cases
- **Module Level**: Share specific module with its test cases only
- **Test Case Level**: Share individual test case only

## Key Features

### ✨ Polymorphic Sharing Architecture
- Single `project_shares` table handles all three types
- Uses `shareable_type` and `shareable_id` for flexibility
- Efficient cascading permissions

### 🔒 Permission Inheritance
- **Project Access** → See all modules and test cases
- **Module Access** → See only that module and its test cases
- **Test Case Access** → See only that specific test case

### 🎯 Smart Access Control
- Owners can share any entity they created
- Two role types: **Editor** (can modify) and **Viewer** (read-only)
- Invitation system with pending/accepted/rejected statuses

## How It Works

### 1. Database Structure
```
project_shares table:
- id
- shareable_type (Project/Module/TestCase)
- shareable_id (ID of the entity)
- shared_with_user_id
- shared_by_user_id
- role (editor/viewer)
- status (pending/accepted/rejected)
- created_at, updated_at
```

### 2. Models Using Shareable Trait
All three models use the `App\Traits\Shareable` trait:
- `App\Modules\Cypress\Models\Project`
- `App\Modules\Cypress\Models\Module`
- `App\Modules\Cypress\Models\TestCase`

**Trait Methods:**
- `shares()` - Get all shares
- `collaborators()` - Get accepted collaborators
- `isOwnedBy($userId)` - Check ownership
- `isSharedWith($userId)` - Check if shared
- `getUserRole($userId)` - Get user's role
- `canEdit($userId)` - Check edit permission
- `canView($userId)` - Check view permission
- `shareWith($user, $role)` - Create share

### 3. Controllers

#### ShareController (Unified)
New controller at `app/Modules/Cypress/Http/Controllers/ShareController.php`

**Routes:**
```php
POST   /share                    // Create share (any type)
GET    /share                    // Get collaborators
DELETE /share/{id}               // Remove collaborator
PUT    /share/{id}/role          // Update role
GET    /invitations/pending      // User's invitations
POST   /invitations/{id}/accept  // Accept invitation
POST   /invitations/{id}/reject  // Reject invitation
```

**Request Format:**
```javascript
{
    "email": "user@example.com",
    "role": "editor",
    "shareable_type": "project",  // or "module" or "testcase"
    "shareable_id": "abc123"
}
```

### 4. Frontend Integration

#### Project Share Button
Located in: `app/Modules/Cypress/resources/views/projects/show.blade.php`
- Share button visible only to project owner
- Modal with email input and role selector
- Real-time collaborator list

#### Module Share Button
Located in: `app/Modules/Cypress/resources/views/modules/show.blade.php`
- Share button visible only to module creator
- Same modal interface as project
- Uses `shareable_type: 'module'`

#### Test Case Share Button
Located in: `app/Modules/Cypress/resources/views/test-cases/show.blade.php`
- Share button visible only to test case creator
- Same modal interface
- Uses `shareable_type: 'testcase'`

### 5. Dashboard Invitations
Updated: `resources/views/dashboard.blade.php`

**Features:**
- Shows invitation type badge (Project/Module/Test Case)
- Color-coded badges:
  - 📁 **Project** - Blue
  - 📦 **Module** - Green
  - 🧪 **Test Case** - Orange
- Accept/Reject buttons
- Email notification support

### 6. Email Notifications
Updated: `resources/views/emails/project-invitation.blade.php`

**Dynamic Content:**
- Shows correct icon based on share type
- Displays shareable name and description
- Role badge (Editor/Viewer)
- One-click accept/decline links

## Usage Examples

### Sharing a Project
```javascript
// From project show page
openShareModal();
// Fill in: email@example.com, role: editor
// Submits to: POST /share
{
    "email": "colleague@example.com",
    "role": "editor",
    "shareable_type": "project",
    "shareable_id": "project_hash_id"
}
```

### Sharing a Module
```javascript
// From module show page
openShareModal();
// Fill in: email@example.com, role: viewer
// Submits to: POST /share
{
    "email": "viewer@example.com",
    "role": "viewer",
    "shareable_type": "module",
    "shareable_id": "module_hash_id"
}
```

### Sharing a Test Case
```javascript
// From test case show page
openTestCaseShareModal();
// Fill in: email@example.com, role: editor
// Submits to: POST /share
{
    "email": "tester@example.com",
    "role": "editor",
    "shareable_type": "testcase",
    "shareable_id": "testcase_hash_id"
}
```

## Permission Cascading Logic

### Scenario 1: Project-Level Share
**User has project access** → Can see:
- ✅ All modules in the project
- ✅ All test cases in all modules
- ✅ Full project details

### Scenario 2: Module-Level Share
**User has module access** → Can see:
- ✅ The specific module
- ✅ All test cases within that module
- ❌ Other modules in the project
- ⚠️ Project appears in list (limited view)

### Scenario 3: Test Case-Level Share
**User has test case access** → Can see:
- ✅ The specific test case
- ❌ Other test cases in the module
- ❌ Module details (minimal)
- ⚠️ Project appears in list (minimal view)

## Implementation in ProjectController

```php
public function show(Project $project)
{
    $userId = auth()->id();
    
    // Check access
    $hasProjectAccess = $project->canView($userId);
    
    if (!$hasProjectAccess) {
        abort(403);
    }

    // Determine what to show
    $showAllModules = $project->isOwnedBy($userId) || 
        $project->shares()
            ->where('shareable_type', Project::class)
            ->where('shared_with_user_id', $userId)
            ->where('status', 'accepted')
            ->exists();

    if ($showAllModules) {
        // Show everything
        $project->load(['modules.testCases']);
    } else {
        // Filter by user's module/test case shares
        $moduleShareIds = [...];
        $testCaseShareIds = [...];
        
        $project->load(['modules' => function($q) use ($moduleShareIds) {
            $q->whereIn('id', $moduleShareIds)
              ->with(['testCases' => function($q2) use ($testCaseShareIds) {
                  $q2->whereIn('id', $testCaseShareIds);
              }]);
        }]);
    }
}
```

## API Response Examples

### Get Collaborators (Module)
```javascript
GET /share?shareable_type=module&shareable_id=abc123

Response:
{
    "success": true,
    "collaborators": [
        {
            "id": 1,
            "role": "editor",
            "status": "accepted",
            "shared_with": {
                "name": "John Doe",
                "email": "john@example.com"
            },
            "shared_by": {
                "name": "Admin User"
            }
        }
    ],
    "is_owner": true
}
```

### Pending Invitations
```javascript
GET /invitations/pending

Response:
{
    "success": true,
    "invitations": [
        {
            "id": 5,
            "role": "editor",
            "status": "pending",
            "shareable_type": "App\\Modules\\Cypress\\Models\\Module",
            "shareable": {
                "name": "Login Module",
                "description": "User authentication tests"
            },
            "shared_by": {
                "name": "Team Lead",
                "email": "lead@example.com"
            }
        }
    ]
}
```

## Benefits

### 🎯 Precision Control
- Share only what's needed
- Reduce clutter for collaborators
- Better security through minimal access

### 📊 Efficient Organization
- Team member sees only relevant test cases
- Module-specific access for specialized testers
- Single test case sharing for quick reviews

### 🔄 Flexible Workflow
- Start with test case share for review
- Upgrade to module share for broader testing
- Grant project access for full collaboration

### 📧 Smart Notifications
- Email clearly states what's being shared
- Visual badges for quick identification
- One-click acceptance from email

## Migration Path

### From Old System
All existing project shares automatically work because:
1. Migration sets `shareable_type` to `Project` by default
2. Renamed `project_id` to `shareable_id`
3. Backward compatibility routes maintained

### Testing the System
1. **Create test data**:
   - Project with multiple modules
   - Modules with multiple test cases

2. **Test project sharing**:
   - Share project → Verify all modules visible

3. **Test module sharing**:
   - Share single module → Verify only that module visible

4. **Test test case sharing**:
   - Share single test case → Verify only that test case visible

5. **Test permissions**:
   - Editor: Can modify
   - Viewer: Read-only

## Best Practices

### When to Use Each Level

**Project Share** - Use when:
- Full team collaboration needed
- Multiple modules being tested
- Long-term project access required

**Module Share** - Use when:
- Specialist testing specific feature
- Temporary module review
- Module-level team separation

**Test Case Share** - Use when:
- Quick peer review needed
- Single scenario validation
- Demo specific functionality
- External stakeholder review

### Security Tips
- Always use "viewer" for external reviewers
- "Editor" only for trusted team members
- Regularly audit collaborator lists
- Remove access when no longer needed

## Troubleshooting

### User Can't See Shared Item
**Check:**
1. Invitation status is "accepted"
2. `shareable_type` matches entity type
3. User logged in with correct account
4. Permission check in controller

### Email Not Sending
**Check:**
1. Mail configuration in `.env`
2. Queue worker running (if using queues)
3. Log files for errors: `storage/logs/laravel.log`

### Share Button Not Visible
**Verify:**
1. User is the creator (`created_by` matches)
2. Blade condition: `@if($entity->created_by === auth()->id())`
3. JavaScript modal function exists

## Future Enhancements

Possible additions:
- [ ] Share expiration dates
- [ ] Share link generation (public shares)
- [ ] Activity logging (who accessed what)
- [ ] Bulk sharing (CSV import)
- [ ] Team/group sharing
- [ ] Advanced permission levels (approve, execute, etc.)

---

**System Status**: ✅ Fully Implemented and Production Ready

**Last Updated**: December 4, 2025
