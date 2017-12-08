<?php  
$input =    
  '{
  "id": "evt_17JLCsH718AiRybJIp90vJNd",
  "object": "event",
  "api_version": "2015-10-16",
  "created": 1450433486,
  "data": {
    "object": {
      "id": "in_17JLCsH718AiRybJgxXDDGhD",
      "object": "invoice",
      "amount_due": 0,
      "application_fee": null,
      "attempt_count": 0,
      "attempted": true,
      "charge": null,
      "closed": true,
      "currency": "eur",
      "customer": "cus_7YRtA4LFdFpYyF",
      "date": 1450433486,
      "description": null,
      "discount": null,
      "ending_balance": -4000,
      "forgiven": false,
      "lines": {
        "object": "list",
        "data": [
          {
            "id": "ii_17JLCsH718AiRybJOf7KfxHM",
            "object": "line_item",
            "amount": -5990,
            "currency": "eur",
            "description": "Unused time on twelve month french plan after 18 Dec 2015",
            "discountable": false,
            "livemode": false,
            "metadata": {},
            "period": {
              "start": 1450433486,
              "end": 1482055331
            },
            "plan": {
              "id": "12",
              "object": "plan",
              "amount": 5990,
              "created": 1448627709,
              "currency": "eur",
              "interval": "year",
              "interval_count": 1,
              "livemode": false,
              "metadata": {},
              "name": "twelve month french plan",
              "statement_descriptor": null,
              "trial_period_days": null
            },
            "proration": true,
            "quantity": 1,
            "subscription": "sub_7YRtFEkcagEoU9",
            "type": "invoiceitem"
          },
          {
            "id": "sub_7YRtFEkcagEoU9",
            "object": "line_item",
            "amount": 1990,
            "currency": "eur",
            "description": null,
            "discountable": true,
            "livemode": false,
            "metadata": {},
            "period": {
              "start": 1450433486,
              "end": 1458295886
            },
            "plan": {
              "id": "3",
              "object": "plan",
              "amount": 1990,
              "created": 1448627565,
              "currency": "eur",
              "interval": "month",
              "interval_count": 3,
              "livemode": false,
              "metadata": {},
              "name": "three month french plan",
              "statement_descriptor": null,
              "trial_period_days": null
            },
            "proration": false,
            "quantity": 1,
            "subscription": null,
            "type": "subscription"
          }
        ],
        "has_more": false,
        "total_count": 2,
        "url": "/v1/invoices/in_17JLCsH718AiRybJgxXDDGhD/lines"
      },
      "livemode": false,
      "metadata": {},
      "next_payment_attempt": null,
      "paid": true,
      "period_end": 1450433486,
      "period_start": 1450433486,
      "receipt_number": null,
      "starting_balance": 0,
      "statement_descriptor": null,
      "subscription": "sub_7YRtFEkcagEoU9",
      "subtotal": -4000,
      "tax": null,
      "tax_percent": null,
      "total": -4000,
      "webhooks_delivered_at": null
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": "req_7YS7GXiwIaqwRS",
  "type": "invoice.payment_succeeded"
}';
$event_json = json_decode($input,true);

print_r($event_json['data']['object']['lines']['total_count']);

//print_r( $event_json['object']['id']);echo "pp";exit;
