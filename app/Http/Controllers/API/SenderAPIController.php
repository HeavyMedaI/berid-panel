<?php
/*
 * File name: UserAPIController.php
 * Last modified: 2021.08.02 at 22:53:11
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SenderAPIController extends Controller
{
    private $userRepository;
    private $uploadRepository;
    private $roleRepository;
    private $customFieldRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, UploadRepository $uploadRepository, RoleRepository $roleRepository, CustomFieldRepository $customFieldRepo)
    {
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
        $this->roleRepository = $roleRepository;
        $this->customFieldRepository = $customFieldRepo;
    }

    function sender(Request $request)
    {
        $sender = $this->userRepository->findByField('id', $request->input('sender_id'))->first();

        if (!$sender) {
            return $this->sendError('Sender not found', 200);
        }

        return $this->sendResponse([
            "id" => $sender->id,
            "name" => $sender->name,
            "email" => $sender->email,
            "phone_number" => $sender->phone_number,
            "media" => $sender->media
        ], 'Sender retrieved successfully');
    }
}
