<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_allows_pjm_to_access_page_only_for_pjm()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm)->get(route('user'))->assertStatus(200);
    }

    public function test_does_not_allow_non_pjm_to_access_page_only_for_pjm()
    {
        $user = User::find(2);
        $this->actingAs($user)->get(route('user'))->assertSessionHasErrors('login_gagal');
    }


    public function test_allows_kajur_to_access_page_only_for_kajur()
    {
        $kajur = User::find(2);
        $this->actingAs($kajur)->get(route('ed_import'))->assertStatus(200);
    }

    public function test_does_not_allow_non_kajur_to_access_page_only_for_kajur()
    {
        $user = User::find(3);
        $this->actingAs($user)->get(route('ed_import'))->assertSessionHasErrors('login_gagal');
    }


    public function test_allows_koorprodi_to_access_page_only_for_koorprodi()
    {
        $koorprodi = User::find(3);
        $this->actingAs($koorprodi)->get('koorprodi')->assertStatus(200);
    }

    public function test_does_not_allow_non_koorprodi_to_access_page_only_for_koorprodi()
    {
        $user = User::find(1);
        $this->actingAs($user)->get('koorprodi')->assertSessionHasErrors('login_gagal');
    }


    public function test_allows_auditor_to_access_page_only_for_auditor()
    {
        $auditor = User::find(4);
        $this->actingAs($auditor)->get('tilik')->assertStatus(200);
    }

    public function test_does_not_allow_non_auditor_to_access_page_only_for_auditor()
    {
        $user = User::find(2);
        $this->actingAs($user)->get('auditor')->assertSessionHasErrors('login_gagal');
    }
}