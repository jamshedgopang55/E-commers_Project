
                $user_email = Auth::user()->email;
                $line_items = [];

                foreach (Cart::content() as $item) {
                    $shippingInfo = shipping::where('country_id', $req->country)->first();
                    $price = $item->price + $shippingInfo->amount;

                    $line_items[] = [
                        'price_data' => [
                            'product_data' => [
                                'name' => $item->name
                            ],
                            'unit_amount' => 100 * $price,

                            'currency' => 'USD'
                        ],
                        'quantity' => $item->qty
                    ];

                };





                $payment_status = 'paid';
                $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
                $successUrl = route('front.thankYou', 12);
                $response = $stripe->checkout->sessions->create([
                    'success_url' => $successUrl,
                    'customer_email' => $user_email,
                    'payment_method_types' => ['link', 'card'],
                    'line_items' => $line_items,
                    'mode' => 'payment',
                    'allow_promotion_codes' => true
                ]);

                return response()->json([
                    'status' => 'test',
                    'url' => $response['url'],
                    'message' => 'Order Save Successfully'
                ]);

            
