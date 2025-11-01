<?php

namespace App\Controllers;

class Landing extends BaseController
{
    public function index(): string
    {

        if (session('id')) {
            sukses(base_url("home"), "You are logged");
        }

        $users = db('user')->orderBy('id', 'ASC')->get()->getResultArray();

        $usernames = [];
        foreach ($users as $i) {
            $exp = explode(" ", $i['nama']);
            $username = strtolower($exp[0]);
            if (in_array($username, $usernames)) {
                $username .= "1";
            }

            $i['username'] = $username;
            $i['password'] = password_hash(settings('password')['value'], PASSWORD_DEFAULT);

            if (db('user')->where('id', $i['id'])->update($i)) {
                $usernames[] = $username;
            }
        }
        return view('guest/landing', ['judul' => profile()['nama']]);
    }
}
