<?php if(!class_exists('raintpl')){exit;}?><div class="adapter">
    <div id="paypal-button" data-redirect="<?php echo $redirect_success;?>" data-token="<?php echo $token;?>"></div>
    <script>
        paypal.Button.render({

            env: "<?php echo $env;?>", // Or "production"
            client: {
                sandbox:    "<?php echo $client_sandbox;?>",
                production: "<?php echo $client_production;?>"
            },
            commit: true, // Show a "Pay Now" button
            style: { 
                size: 'small',
                color: 'silver',
                shape: 'rect'
            },

            payment: function(data, actions) {
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
                                amount: { total: "<?php echo $amount;?>", currency: "<?php echo $currency;?>" }
                            }
                        ]
                    }
                });
            },

            onAuthorize: function(data, actions) {
                return actions.payment.execute().then(function(payment) {
                    var button = $("#paypal-button");
                    window.location.href = button.data('redirect') + '?token=' + button.data('token');
                });
            }

        }, "#paypal-button");
    </script>
</div>