<?php
namespace App\Controllers;

use App\Models\Group;
use App\Models\GroupMember;

class GroupController
{
    private Group $groups;
    private GroupMember $members;

    public function __construct(\PDO $pdo)
    {
        $this->groups = new Group($pdo);
        $this->members = new GroupMember($pdo);
    }

    public function index()
    {
        $all = $this->groups->all();
        return view('groups/index', ['title' => 'Gruppen', 'groups' => $all]);
    }

    public function create()
    {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            return redirect('/groups');
        }
        $id = $this->groups->create(auth_user()['id'], $name);
        $this->members->add($id, auth_user()['id'], 'owner');
        redirect('/groups');
    }

    public function join()
    {
        $code = trim($_POST['invite_code'] ?? '');
        $group = $this->groups->findByInvite($code);
        if ($group) {
            $this->members->add((int)$group['id'], auth_user()['id']);
        }
        redirect('/groups');
    }

    public function removeMember(array $params)
    {
        $gid = (int)$params['id'];
        $uid = (int)($_POST['user_id'] ?? 0);
        $this->members->remove($gid, $uid);
        redirect('/groups');
    }
}
