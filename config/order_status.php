<?php

return $orderStatuses = [
    'pending'         => 'Order is pending, and waiting for confirm',
    'processing'      => 'Order confirmed, items are being prepared',
    'packed'          => 'Items have been packaged and labeled',
    'shipped'         => 'Order has been handed over to the courier',
    'in_transit'      => 'Order is moving through the delivery network',
    'out_for_delivery' => 'Courier is on the way to the delivery address',
    'delivered'       => 'Order successfully delivered to the customer',
    'canceled'       =>  'Order is canceled by the owner',
];
