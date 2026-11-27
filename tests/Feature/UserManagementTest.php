<?php

use App\Filament\Resources\Roles\Pages\ManageRoles;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\PanelRegistry;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'Super Admin']);
    $this->manager = User::factory()->create()->givePermissionTo('manage users');
});

test('users with the manage users permission can open the panel and its access pages only', function () {
    expect($this->manager->canAccessPanel(app(PanelRegistry::class)->getDefault()))->toBeTrue();

    $this->actingAs($this->manager);

    $this->get('/admin/users')->assertOk();
    $this->get('/admin/roles')->assertOk();
    $this->get('/admin/permissions')->assertOk();
    $this->get('/admin/steam-games')->assertForbidden();
});

test('users without the permission cannot manage users', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/users')
        ->assertForbidden();
});

test('a user created in the panel is verified and gets its roles and password', function () {
    $role = Role::create(['name' => 'Editor']);

    $this->actingAs($this->manager);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'a-long-test-password',
            'roles' => [$role->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'test@example.com')->firstOrFail();

    expect($user->hasVerifiedEmail())->toBeTrue()
        ->and(Hash::check('a-long-test-password', $user->password))->toBeTrue()
        ->and($user->hasRole('Editor'))->toBeTrue();
});

test('editing a user without a password keeps the current one', function () {
    $user = User::factory()->create();
    $originalHash = $user->password;

    $this->actingAs($this->manager);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->fillForm(['name' => 'Renamed', 'password' => ''])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh())
        ->name->toBe('Renamed')
        ->password->toBe($originalHash);
});

test('a user manager cannot grant or edit super admin', function () {
    $superAdmin = User::factory()->create()->assignRole('Super Admin');

    $this->actingAs($this->manager);

    Livewire::test(CreateUser::class)
        ->assertFormFieldExists('roles', fn (Select $field): bool => ! in_array('Super Admin', $field->getOptions()));

    expect($this->manager->can('update', $superAdmin))->toBeFalse()
        ->and($this->manager->can('delete', $this->manager))->toBeFalse()
        ->and($this->manager->can('update', Role::findByName('Super Admin')))->toBeFalse()
        ->and($this->manager->can('delete', Permission::findByName('manage users')))->toBeFalse();
});

test('a role can be given permissions', function () {
    $role = Role::create(['name' => 'Moderator']);
    $permission = Permission::create(['name' => 'moderate aliases']);

    $this->actingAs($this->manager);

    Livewire::test(ManageRoles::class)
        ->callAction(TestAction::make('edit')->table($role), [
            'name' => 'Moderator',
            'permissions' => [$permission->id],
        ])
        ->assertHasNoFormErrors();

    expect($role->fresh()->hasPermissionTo('moderate aliases'))->toBeTrue();
});
