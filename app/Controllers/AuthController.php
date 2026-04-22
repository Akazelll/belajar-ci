<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
    }
    function __construct()
    {
        helper('form');
    }
    public function login()
    {
        if ($this->request->getPost()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            $dataUser = ['username' => 'april', 'password' => '202cb962ac59075b964b07152d234b70', 'role' => 'admin']; // passw 123

            if ($username == $dataUser['username']) {
                if (md5($password) == $dataUser['password']) {
                    session()->set([
                        'username' => $dataUser['username'],
                        'role' => $dataUser['role'],
                        'isLoggedIn' => TRUE
                    ]);

                    return redirect()->to(base_url('/'));
                } else {
                    session()->setFlashdata('failed', 'Username & Password Salah');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                return redirect()->back();
            }
        } else {
            return view('v_login');
        }
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
