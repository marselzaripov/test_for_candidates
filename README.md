Symfony test for candidate

1. GET request for price calculating

http://localhost:8000/api/v1/product/calculate

json body:

{
    "product_id": 1,
    "taxNumber": "DE123456789",
    "couponCode": "D15"
}

json response:

200 OK
{
    "price": 1003.44
}

2. POST request for price calculating

http://localhost:8000/api/v1/product/purchase

json body:

{
    "product_id": 1,
    "taxNumber": "DE123456789",
    "couponCode": "D15",
    "paymentProcessor": "paypal"
}

json response:

200 OK
{
    "message": "success"
}
