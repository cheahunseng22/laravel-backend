from bakong_khqr import KHQR
import json
import sys

args = sys.argv[1:]
if len(args) != 4:
    print(json.dumps({"error": "Invalid number of arguments"}))
    sys.exit(1)

bank_account, amount_str, merchant_name, merchant_city = args

try:
    amount = float(amount_str)
except ValueError:
    print(json.dumps({"error": "Amount must be a number"}))
    sys.exit(1)

# Initialize KHQR with your JWT token
khqr = KHQR("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiMjI1ZjUwOGZlM2Y1NDg1YiJ9LCJpYXQiOjE3NTAyOTk2MzgsImV4cCI6MTc1ODA3NTYzOH0.NJxWEV12UDUJD2u7GmgVN9WawCBLpg4z91DdIZMt08s")

qr_data = {
    'bank_account': bank_account,
    'merchant_name': merchant_name,
    'merchant_city': merchant_city,
    'amount': amount,
    'currency': 'USD',   
    'store_label': 'Cheahun',
    'phone_number': '8550967551164',
    'bill_number': 'TRX01234567',
    'terminal_label': 'Cashier-01',
    'static': False
}

qr_data_str = json.dumps(qr_data)

# Generate md5 hash from string representation of qr_data
md5 = khqr.generate_md5(qr_data_str)

# Generate the QR code string
qr = khqr.create_qr(**qr_data)

# Check payment status by md5
payment_status = khqr.check_payment(md5)

# Output all as JSON
print(json.dumps({
    "qr": qr,
    "md5": md5,
    "payment_status": payment_status
    
}))
