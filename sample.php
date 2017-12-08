<?php
Stripe\Customer Object
(
    [_opts:protected] => Stripe\Util\RequestOptions Object
        (
            [headers] => Array
                (
                )

            [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
        )

    [_values:protected] => Array
        (
            [id] => cus_7PNHO1IyTkEVkU
            [object] => customer
            [account_balance] => 0
            [created] => 1448339774
            [currency] => eur
            [default_source] => 
            [delinquent] => 
            [description] => 
            [discount] => 
            [email] => neethu12345@example.com
            [livemode] => 
            [metadata] => Stripe\AttachedObject Object
                (
                    [_opts:protected] => Stripe\Util\RequestOptions Object
                        (
                            [headers] => Array
                                (
                                )

                            [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                        )

                    [_values:protected] => Array
                        (
                        )

                    [_unsavedValues:protected] => Stripe\Util\Set Object
                        (
                            [_elts:Stripe\Util\Set:private] => Array
                                (
                                )

                        )

                    [_transientValues:protected] => Stripe\Util\Set Object
                        (
                            [_elts:Stripe\Util\Set:private] => Array
                                (
                                )

                        )

                    [_retrieveOptions:protected] => Array
                        (
                        )

                )

            [shipping] => 
            [sources] => Stripe\Collection Object
                (
                    [_opts:protected] => Stripe\Util\RequestOptions Object
                        (
                            [headers] => Array
                                (
                                )

                            [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                        )

                    [_values:protected] => Array
                        (
                            [object] => list
                            [data] => Array
                                (
                                )

                            [has_more] => 
                            [total_count] => 0
                            [url] => /v1/customers/cus_7PNHO1IyTkEVkU/sources
                        )

                    [_unsavedValues:protected] => Stripe\Util\Set Object
                        (
                            [_elts:Stripe\Util\Set:private] => Array
                                (
                                )

                        )

                    [_transientValues:protected] => Stripe\Util\Set Object
                        (
                            [_elts:Stripe\Util\Set:private] => Array
                                (
                                )

                        )

                    [_retrieveOptions:protected] => Array
                        (
                        )

                )

            [subscriptions] => Stripe\Collection Object
                (
                    [_opts:protected] => Stripe\Util\RequestOptions Object
                        (
                            [headers] => Array
                                (
                                )

                            [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                        )

                    [_values:protected] => Array
                        (
                            [object] => list
                            [data] => Array
                                (
                                    [0] => Stripe\Subscription Object
                                        (
                                            [_opts:protected] => Stripe\Util\RequestOptions Object
                                                (
                                                    [headers] => Array
                                                        (
                                                        )

                                                    [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                                                )

                                            [_values:protected] => Array
                                                (
                                                    [id] => sub_7PNHIv8hcp0Qax
                                                    [object] => subscription
                                                    [application_fee_percent] => 
                                                    [cancel_at_period_end] => 
                                                    [canceled_at] => 
                                                    [current_period_end] => 1448512574
                                                    [current_period_start] => 1448339774
                                                    [customer] => cus_7PNHO1IyTkEVkU
                                                    [discount] => 
                                                    [ended_at] => 
                                                    [metadata] => Stripe\AttachedObject Object
                                                        (
                                                            [_opts:protected] => Stripe\Util\RequestOptions Object
                                                                (
                                                                    [headers] => Array
                                                                        (
                                                                        )

                                                                    [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                                                                )

                                                            [_values:protected] => Array
                                                                (
                                                                )

                                                            [_unsavedValues:protected] => Stripe\Util\Set Object
                                                                (
                                                                    [_elts:Stripe\Util\Set:private] => Array
                                                                        (
                                                                        )

                                                                )

                                                            [_transientValues:protected] => Stripe\Util\Set Object
                                                                (
                                                                    [_elts:Stripe\Util\Set:private] => Array
                                                                        (
                                                                        )

                                                                )

                                                            [_retrieveOptions:protected] => Array
                                                                (
                                                                )

                                                        )

                                                    [plan] => Stripe\Plan Object
                                                        (
                                                            [_opts:protected] => Stripe\Util\RequestOptions Object
                                                                (
                                                                    [headers] => Array
                                                                        (
                                                                        )

                                                                    [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                                                                )

                                                            [_values:protected] => Array
                                                                (
                                                                    [id] => 1
                                                                    [object] => plan
                                                                    [amount] => 990
                                                                    [created] => 1447921207
                                                                    [currency] => eur
                                                                    [interval] => day
                                                                    [interval_count] => 1
                                                                    [livemode] => 
                                                                    [metadata] => Stripe\AttachedObject Object
                                                                        (
                                                                            [_opts:protected] => Stripe\Util\RequestOptions Object
                                                                                (
                                                                                    [headers] => Array
                                                                                        (
                                                                                        )

                                                                                    [apiKey] => sk_test_9JT3WooXZ8ig9zIcbEKL6rVZ
                                                                                )

                                                                            [_values:protected] => Array
                                                                                (
                                                                                )

                                                                            [_unsavedValues:protected] => Stripe\Util\Set Object
                                                                                (
                                                                                    [_elts:Stripe\Util\Set:private] => Array
                                                                                        (
                                                                                        )

                                                                                )

                                                                            [_transientValues:protected] => Stripe\Util\Set Object
                                                                                (
                                                                                    [_elts:Stripe\Util\Set:private] => Array
                                                                                        (
                                                                                        )

                                                                                )

                                                                            [_retrieveOptions:protected] => Array
                                                                                (
                                                                                )

                                                                        )

                                                                    [name] => one month
                                                                    [statement_descriptor] => one month
                                                                    [trial_period_days] => 2
                                                                )

                                                            [_unsavedValues:protected] => Stripe\Util\Set Object
                                                                (
                                                                    [_elts:Stripe\Util\Set:private] => Array
                                                                        (
                                                                        )

                                                                )

                                                            [_transientValues:protected] => Stripe\Util\Set Object
                                                                (
                                                                    [_elts:Stripe\Util\Set:private] => Array
                                                                        (
                                                                        )

                                                                )

                                                            [_retrieveOptions:protected] => Array
                                                                (
                                                                )

                                                        )

                                                    [quantity] => 1
                                                    [start] => 1448339774
                                                    [status] => trialing
                                                    [tax_percent] => 
                                                    [trial_end] => 1448512574
                                                    [trial_start] => 1448339774
                                                )

                                            [_unsavedValues:protected] => Stripe\Util\Set Object
                                                (
                                                    [_elts:Stripe\Util\Set:private] => Array
                                                        (
                                                        )

                                                )

                                            [_transientValues:protected] => Stripe\Util\Set Object
                                                (
                                                    [_elts:Stripe\Util\Set:private] => Array
                                                        (
                                                        )

                                                )

                                            [_retrieveOptions:protected] => Array
                                                (
                                                )

                                        )

                                )

                            [has_more] => 
                            [total_count] => 1
                            [url] => /v1/customers/cus_7PNHO1IyTkEVkU/subscriptions
                        )

                    [_unsavedValues:protected] => Stripe\Util\Set Object
                        (
                            [_elts:Stripe\Util\Set:private] => Array
                                (
                                )

                        )

                    [_transientValues:protected] => Stripe\Util\Set Object
                        (
                            [_elts:Stripe\Util\Set:private] => Array
                                (
                                )

                        )

                    [_retrieveOptions:protected] => Array
                        (
                        )

                )

        )

    [_unsavedValues:protected] => Stripe\Util\Set Object
        (
            [_elts:Stripe\Util\Set:private] => Array
                (
                )

        )

    [_transientValues:protected] => Stripe\Util\Set Object
        (
            [_elts:Stripe\Util\Set:private] => Array
                (
                )

        )

    [_retrieveOptions:protected] => Array
        (
        )

)
?>
