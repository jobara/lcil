<?php

use App\Models\LawPolicySource;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Law and Policy Sources
Breadcrumbs::for('lawPolicySources.index', function (BreadcrumbTrail $trail) {
    $trail->push(__('Law and Policy Sources'), \localized_route('lawPolicySources.index'));
});

// Law and Policy Sources > Create a Law or Policy Source
Breadcrumbs::for('lawPolicySources.create', function (BreadcrumbTrail $trail) {
    $trail->parent('lawPolicySources.index');
    $trail->push(__('Create a Law or Policy Source'));
});

// Law and Policy Sources > [Law and Policy Source]
Breadcrumbs::for('lawPolicySources.show', function (BreadcrumbTrail $trail, LawPolicySource $lawPolicySource) {
    $trail->parent('lawPolicySources.index');
    $trail->push($lawPolicySource->name, \localized_route('lawPolicySources.show', $lawPolicySource));
});

// Law and Policy Sources > [Law and Policy Source] > Edit Law or Policy Source
Breadcrumbs::for('lawPolicySources.edit', function (BreadcrumbTrail $trail, LawPolicySource $lawPolicySource) {
    $trail->parent('lawPolicySources.show', $lawPolicySource);
    $trail->push(__('Edit Law or Policy Source'));
});

// Law and Policy Sources > [Law and Policy Source] > Add Provision
Breadcrumbs::for('provisions.create', function (BreadcrumbTrail $trail, LawPolicySource $lawPolicySource) {
    $trail->parent('lawPolicySources.show', $lawPolicySource);
    $trail->push(__('Add Provision'));
});

// Law and Policy Sources > [Law and Policy Source] > Edit Provision
Breadcrumbs::for('provisions.edit', function (BreadcrumbTrail $trail, LawPolicySource $lawPolicySource) {
    $trail->parent('lawPolicySources.show', $lawPolicySource);
    $trail->push(__('Edit Provision'));
});
