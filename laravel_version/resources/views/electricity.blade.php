@extends('coming-soon')

@php
    $pageTitle = 'Electricity Bills';
    $pageDescription = 'Pay electricity bills for all distribution companies';
    $pageIcon = 'fas fa-bolt';
    $pageColor = 'yellow';
    $features = [
        'All DISCO companies supported',
        'Prepaid and postpaid meters',
        'Token generation for prepaid',
        'Bill payment history',
        'Automatic meter validation'
    ];
@endphp
