<?php
$input = 
  '{ 
	  "created": 1326853478,
  "livemode": false,
  "id": "evt_00000000000000",
  "type": "invoice.payment_succeeded",
  "object": "event",
  "request": null,
  "pending_webhooks": 1,
  "api_version": "2015-10-16",
  "data": {
    "object": {
      "id": "in_00000000000000",
      "object": "invoice",
      "amount_due": 990,
      "application_fee": null,
      "attempt_count": 0,
      "attempted": true,
      "charge": "_00000000000000",
      "closed": true,
      "currency": "eur",
      "customer": "cus_00000000000000",
      "date": 1449211708,
      "description": null,
      "discount": null,
      "ending_balance": null,
      "forgiven": false,
      "lines": {
        "data": [
          {
            "id": "sub_7TA18CsRNsYuqF",
            "object": "line_item",
            "amount": 990,
            "currency": "eur",
            "description": null,
            "discountable": true,
            "livemode": true,
            "metadata": {},
            "period": {
              "start": 1451891389,
              "end": 1454569789
            },
            "plan": {
              "id": "1",
              "object": "plan",
              "amount": 990,
              "created": 1448627489,
              "currency": "eur",
              "interval": "month",
              "interval_count": 1,
              "livemode": false,
              "metadata": {},
              "name": "one month french plan",
              "statement_descriptor": null,
              "trial_period_days": null
            },
            "proration": false,
            "quantity": 1,
            "subscription": null,
            "type": "subscription"
          }
        ],
        "total_count": 1,
        "object": "list",
        "url": "/v1/invoices/in_17EDMmH718AiRybJJD9a5rax/lines"
      },
      "livemode": false,
      "metadata": {},
      "next_payment_attempt": 1449215308,
      "paid": true,
      "period_end": 1449211617,
      "period_start": 1449125217,
      "receipt_number": null,
      "starting_balance": 0,
      "statement_descriptor": null,
      "subscription": "sub_00000000000000",
      "subtotal": 990,
      "tax": null,
      "tax_percent": null,
      "total": 990,
      "webhooks_delivered_at": 1449211779
    }
  }}';

$event_json = json_decode($input,true);
//echo $sub  = $event_json['data']['object']['lines']['data'][0]['plan']['id'];
$month				=	"2 month";
$payment_expiry_date	=	date("Y-m-d", strtotime($month));	echo $event_json['data']['object']['lines']['total_count'];
?>
