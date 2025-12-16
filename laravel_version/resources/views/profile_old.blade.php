@extends('coming-soon')

@php
    $pageTitle = 'User Profile';
    $pageDescription = 'Manage your account information and settings';
    $pageIcon = 'fas fa-user';
    $pageColor = 'blue';
    $features = [
        'Edit personal information',
        'Change password',
        'Update phone number/email',
        'Account security settings',
        'Profile picture upload'
    ];
@endphp
