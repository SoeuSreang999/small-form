<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;


Breadcrumbs::for('dashboard', function (Trail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

Breadcrumbs::for('home', function (Trail $trail) {
    $trail->push(__('menu.home'), route('home'));
});

Breadcrumbs::for('setups.users.index', function (Trail $trail) {
    $trail->parent('home');
    $trail->push('Users', route('setups.users.index'));
});

Breadcrumbs::for('classes.subject.index', function (Trail $trail) {
    $trail->parent('home');
    $trail->push(__('menu.subjects'), route('classes.subject.index'));
});

Breadcrumbs::for('classes.class.index', function (Trail $trail) {
    $trail->parent('home');
    $trail->push(__('menu.classes'), route('classes.class.index'));
});

// forms
Breadcrumbs::for('forms.index', function (Trail $trail) {
    $trail->parent('home');
    $trail->push(__('menu.forms'), route('forms.list'));
});

Breadcrumbs::for('forms.list', function (Trail $trail) {
    $trail->parent('home');
    $trail->push(__('menu.forms'), route('forms.list'));
});

Breadcrumbs::for('forms.edit', function (Trail $trail) {
    $trail->parent('home');
    $trail->push(__('menu.forms'), route('forms.list'));
});