@extends('coming-soon')

@php
    $pageTitle = 'Transaction History';
    $pageDescription = 'View and manage all your transaction records';
    $pageIcon = 'fas fa-history';
    $pageColor = 'gray';
    $features = [
        'Complete transaction history',
        'Advanced filtering options',
        'Export to PDF/Excel',
        'Transaction receipt download',
        'Search by date, amount, type'
    ];
@endphp
