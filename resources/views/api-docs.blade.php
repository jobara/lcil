<x-app-layout>
    <x-slot name="title">{{ __('API') }}</x-slot>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('API') }}</h1>
    </x-slot>


    {!!
        Str::markdown(__('The API provides a way to retrieve data as JSON. To access the API you will need to use an [API Token](:tokenURL),
        which will should be included in the `Aunthentication` header as a `Bearer` token in the request. The request will also need to set
        the `Accept` header to `application/json`.', [
            'tokenURL' => localized_route('tokens.show'),
        ]))
    !!}

    @auth
        {{-- Evaluation End Point --}}
        <h2>{{ __('Retrieve a specific Evaluation') }}</h2>

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.evaluations.show'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Evaluations end point') }}">
            <tbody>
                <tr>
                    <th>Path Paramaters</th>
                    <td>
                        <dl>
                            <dt>evaluation</dt>
                            <dd>{!! Str::inlinemarkdown(__('An Evaulation’s `id`.')) !!}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": {
        "id": 123,
        "assessment": "partially",
        "comment": null,
        "regimeAssessment": {
            ...
        },
        "measure": {
            ...
        },
        "provision": {
            ...
        },
        "created_at": "",
        "updated_at": ""
    }
}
</code></pre>

        {{-- Evaluations End Point --}}
        <h2>{{ __('Retrieve a list of Evaluations') }}</h2>

        {!!
            Str::markdown(__('Returns a set of [paginated](:paginationURL) Evaluations. Optionally, query parameters may
            be provided to filter the results.', [
                'paginationURL' => 'https://laravel.com/docs/9.x/eloquent-resources#pagination',
            ]))
        !!}

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.evaluations.index'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Evaluations end point') }}">
            <tbody>
                <tr>
                    <th>Query Paramaters</th>
                    <td>
                        <dl>
                            <dt>ra_id</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Regime Assessment’s `ra_id`.')) !!}</dd>
                        </dl>
                        <dl>
                            <dt>measureCode</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Measure’s `code`.')) !!}</dd>
                        </dl>
                        <dl>
                            <dt>provisionID</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Provision’s `id`.')) !!}</dd>
                        </dl>
                        <dl>
                            <dt>assessment</dt>
                            <dd>{!! Str::inlinemarkdown(__('An Assessment value; :assessments.', [
                                'assessments' => implode(', ', App\Enums\EvaluationAssessments::values())
                            ])) !!}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": [
        {
            "id": 123,
            "assessment": "not_at_all",
            "comment": "A comment.",
            "regimeAssessment": {
                ...
            },
            "measure": {
                ...
            },
            "provision": {
                ...
            },
            "created_at": "",
            "updated_at": ""
        },
        {
            "id": 124,
            "assessment": "partially",
            "comment": "The comment.",
            "regimeAssessment": {
                ...
            },
            "measure": {
                ...
            },
            "provision": {
                ...
            },
            "created_at": "",
            "updated_at": ""
        },
        ...
    ],
    "links": {
        "first": "{{ __('URL to first page') }}",
        "last": "{{ __('URL to last page') }}",
        "prev": "{{ __('URL to previous page') }}",
        "next": "{{ __('URL to next page') }}"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "links": [
            ...
        ],
        "path": "{{ __('URL to Evaluations') }}",
        "per_page": 15,
        "to": 15,
        "total": 40
    }
}
</code></pre>

        {{-- Law or Policy Source End Point --}}
        <h2>{{ __('Retrieve a specific Law or Policy Source') }}</h2>

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.lawPolicySources.show'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Law or Policy Source end point') }}">
            <tbody>
                <tr>
                    <th>Path Paramaters</th>
                    <td>
                        <dl>
                            <dt>lawPolicySource</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Law or Policy Source’s `slug`.')) !!}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": {
        "id": 126,
        "name": "Test",
        "type": "constitutional",
        "is_core": 1,
        "reference": "http://example.com",
        "jurisdiction": "CA-ON",
        "jurisdiction_name": "Toronto, Ontario, Canada",
        "municipality": "Toronto",
        "year_in_effect": 2022,
        "provisions": [
            ...
        ],
        "provisions_count": 2,
        "slug": "ca-on-test",
        "created_at": "",
        "updated_at": ""
    }
}
</code></pre>

        {{-- Law and Policy Sources End Point --}}
        <h2>{{ __('Retrieve a list of Law and Policy Sources') }}</h2>

        {!!
            Str::markdown(__('Returns a set of [paginated](:paginationURL) Law and Policy Sources. Optionally, query parameters may
            be provided to filter the results.', [
                'paginationURL' => 'https://laravel.com/docs/9.x/eloquent-resources#pagination',
            ]))
        !!}

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.lawPolicySources.index'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Law and Policy Sources end point') }}">
            <tbody>
                <tr>
                    <th>Query Paramaters</th>
                    <td>
                        <dl>
                            <dt>country</dt>
                            <dd>{{ __('An ISO 3166-1 alpha-2 country code.') }}</dd>
                        </dl>
                        <dl>
                            <dt>subdivision</dt>
                            <dd>{{ __('The subdivision portion of an ISO-3166-2 code.') }}</dd>
                        </dl>
                        <dl>
                            <dt>keywords</dt>
                            <dd>{{ __('A space separated list of keywords to search for in a Law or Policy Source’s name. Will return results for each of the keywords') }}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": [
        {
            "id": 124,
            "name": "Alice opened the door between us. For instance.",
            "type": null,
            "is_core": 1,
            "reference": "http://jast.com/",
            "jurisdiction": "BD",
            "jurisdiction_name": "Bangladesh",
            "municipality": null,
            "year_in_effect": null,
            "provisions": [
                ...
            ],
            "provisions_count": 5,
            "slug": "bd-alice-opened-the-door-between-us-for-instance",
            "created_at": "",
            "updated_at": ""
        },
        {
            "id": 121,
            "name": "As she said this, she looked up and leave the.",
            "type": null,
            "is_core": 0,
            "reference": "https://www.satterfield.com/qui-ducimus-quam-maiores-ut-quae",
            "jurisdiction": "BD",
            "jurisdiction_name": "Bangladesh",
            "municipality": null,
            "year_in_effect": 1909,
            "provisions": [
                ...
            ],
            "provisions_count": 5,
            "slug": "bd-as-she-said-this-she-looked-up-and-leave-the",
            "created_at": "",
            "updated_at": ""
        },
        ...
    ],
    "links": {
        "first": "{{ __('URL to first page') }}",
        "last": "{{ __('URL to last page') }}",
        "prev": "{{ __('URL to previous page') }}",
        "next": "{{ __('URL to next page') }}"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "links": [
            ...
        ],
        "path": "{{ __('URL to Law and Policy Sources') }}",
        "per_page": 15,
        "to": 15,
        "total": 40
    }
}
</code></pre>

        {{-- Regime Assessment End Point --}}
        <h2>{{ __('Retrieve a specific Regime Assessment') }}</h2>

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.regimeAssessments.show'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Regime Assessment end point') }}">
            <tbody>
                <tr>
                    <th>Path Paramaters</th>
                    <td>
                        <dl>
                            <dt>regimeAssessment</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Regime Assessment’s `ra_id`.')) !!}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": {
        "id": 26,
        "jurisdiction": "CA-ON",
        "jurisdiction_name": "Toronto, Ontario, Canada",
        "municipality": "Toronto",
        "description": "Test Regime Assessment",
        "year_of_assessment": 2022,
        "status": "draft",
        "lawPolicySources": [
            ...
        ],
        "lawPolicySources_count": 1,
        "evaluations": [
            ...
        ],
        "evaluations_count": 4,
        "ra_id": "ra-20221017",
        "created_at": "",
        "updated_at": ""
    }
}
</code></pre>

        {{-- Regime Assessments End Point --}}
        <h2>{{ __('Retrieve a list of Regime Assessments') }}</h2>

        {!!
            Str::markdown(__('Returns a set of [paginated](:paginationURL) Regime Assessments. Optionally, query parameters may
            be provided to filter the results.', [
                'paginationURL' => 'https://laravel.com/docs/9.x/eloquent-resources#pagination',
            ]))
        !!}

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.regimeAssessments.index'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Regime Assessments end point') }}">
            <tbody>
                <tr>
                    <th>Query Paramaters</th>
                    <td>
                        <dl>
                            <dt>country</dt>
                            <dd>{{ __('An ISO 3166-1 alpha-2 country code.') }}</dd>
                        </dl>
                        <dl>
                            <dt>subdivision</dt>
                            <dd>{{ __('The subdivision portion of an ISO-3166-2 code.') }}</dd>
                        </dl>
                        <dl>
                            <dt>status</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Status value; :statuses.', [
                                'statuses' => implode(', ', App\Enums\RegimeAssessmentStatuses::values())
                            ])) !!}</dd>
                        </dl>
                        <dl>
                            <dt>keywords</dt>
                            <dd>{{ __('A space separated list of keywords to search for in a Regime Assessment’s description. Will return results for each of the keywords') }}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": [
        {
            "id": 13,
            "jurisdiction": "CA-NS",
            "jurisdiction_name": "Nova Scotia, Canada",
            "municipality": null,
            "description": null,
            "year_of_assessment": null,
            "status": "needs_review",
            "lawPolicySources": [

            ],
            "lawPolicySources_count": 5,
            "evaluations": [
                ...
            ],
            "evaluations_count": 25,
            "ra_id": "ra-20221013-12",
            "created_at": "",
            "updated_at": ""
        },
        {
            "id": 19,
            "jurisdiction": "CA-NU",
            "jurisdiction_name": "Nunavut, Canada",
            "municipality": null,
            "description": null,
            "year_of_assessment": 1800,
            "status": "needs_review",
            "lawPolicySources": [
                ...
            ],
            "lawPolicySources_count": 5,
            "evaluations": [
                ...
            ],
            "evaluations_count": 25,
            "ra_id": "ra-20221013-18",
            "created_at": "",
            "updated_at": ""
        },
        ...
    ],
    "links": {
        "first": "{{ __('URL to first page') }}",
        "last": "{{ __('URL to last page') }}",
        "prev": "{{ __('URL to previous page') }}",
        "next": "{{ __('URL to next page') }}"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "links": [
            ...
        ],
        "path": "{{ __('URL to Regime Assessments') }}",
        "per_page": 15,
        "to": 15,
        "total": 40
    }
}
</code></pre>

        {{-- Regime Assessment Evaluations End Point --}}
        <h2>{{ __('Retrieve a list of Evaluations related for a Regime Assessment') }}</h2>

        {!!
            Str::markdown(__('Returns a set of [paginated](:paginationURL) Evaluations for the specified Regime Assessment.
            Optionally, query parameters may be provided to filter the results.', [
                'paginationURL' => 'https://laravel.com/docs/9.x/eloquent-resources#pagination',
            ]))
        !!}

        <dl>
            <dt>{{ __('URI') }}</dt>
            <dd><code>{{ $endPoints['api.regimeAssessments.evaluations'] }}</code></dd>
            <dt>{{ __('Method') }}</dt>
            <dd>{{ __('GET') }}</dd>
        </dl>

        <h3>Parameters</h3>

        <table summar="{{ __('Parameters for the Regime Assessment Evaluations end point') }}">
            <tbody>
                <tr>
                    <th>Path Paramaters</th>
                    <td>
                        <dl>
                            <dt>regimeAssessment</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Regime Assessment’s `ra_id`.')) !!}</dd>
                        </dl>
                    </td>
                </tr>
                <tr>
                    <th>Query Paramaters</th>
                    <td>
                        <dl>
                            <dt>measureCode</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Measure’s `code`.')) !!}</dd>
                        </dl>
                        <dl>
                            <dt>provisionID</dt>
                            <dd>{!! Str::inlinemarkdown(__('A Provision’s `id`.')) !!}</dd>
                        </dl>
                        <dl>
                            <dt>assessment</dt>
                            <dd>{!! Str::inlinemarkdown(__('An Assessment value; :assessments.', [
                                'assessments' => implode(', ', App\Enums\EvaluationAssessments::values())
                            ])) !!}</dd>
                        </dl>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Example Response</h3>

<pre class="card"><code>
{
    "data": [
        {
            "id": 123,
            "assessment": "not_at_all",
            "comment": "A comment.",
            "regimeAssessment": {
                ...
            },
            "measure": {
                ...
            },
            "provision": {
                ...
            },
            "created_at": "",
            "updated_at": ""
        },
        {
            "id": 124,
            "assessment": "partially",
            "comment": "The comment.",
            "regimeAssessment": {
                ...
            },
            "measure": {
                ...
            },
            "provision": {
                ...
            },
            "created_at": "",
            "updated_at": ""
        },
        ...
    ],
    "links": {
        "first": "{{ __('URL to first page') }}",
        "last": "{{ __('URL to last page') }}",
        "prev": "{{ __('URL to previous page') }}",
        "next": "{{ __('URL to next page') }}"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "links": [
            ...
        ],
        "path": "{{ __('URL to Evaluations') }}",
        "per_page": 15,
        "to": 15,
        "total": 40
    }
}
</code></pre>

    @endauth
</x-app-layout>
