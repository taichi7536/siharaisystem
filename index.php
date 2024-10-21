<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>支払情報を入力してください</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>支払情報を入力してください</h1>
    <form id="paymentForm">
        <label for="customerNumber">お客様番号（13桁）</label>
        <input type="text" id="customerNumber" maxlength="13" required>

        <label for="paymentMonth">月分</label>
        <select id="paymentMonth" required></select>

        <label for="paymentDate">支払日</label>
        <input type="date" id="paymentDate" required>

        <label for="financialInstitution">支払った金融機関</label>
        <select id="financialInstitution" required>
            <option value="銀行">銀行</option>
            <option value="郵便局">郵便局</option>
        </select>

        <button type="submit">送信</button>
    </form>

    <div id="confirmation" style="display: none;">
        <h2>この内容で送信しますか？</h2>
        <p id="confirmDetails"></p>
        <button id="confirmButton">はい、送信します</button>
    </div>

    <div id="completeMessage" style="display: none;">
        <h2>支払済み連絡が完了しました。ありがとうございました。</h2>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const paymentMonthSelect = document.getElementById('paymentMonth');
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth() + 1; // 現在の月（1-12）
        
        // 現在の月から5ヶ月前までを選択肢に追加
        for (let i = 0; i < 6; i++) {
            const paymentMonth = new Date(year, month - i - 1, 1);
            const yearReiwa = paymentMonth.getFullYear() - 2018; // 令和は2019年（令和元年）から
            const monthOption = `${yearReiwa}年${paymentMonth.getMonth() + 1}月`;
            const option = document.createElement('option');
            option.value = monthOption;
            option.textContent = monthOption;
            paymentMonthSelect.appendChild(option);
        }

        // 送信処理
        const form = document.getElementById('paymentForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const customerNumber = document.getElementById('customerNumber').value;
            const paymentMonth = document.getElementById('paymentMonth').value;
            const paymentDate = document.getElementById('paymentDate').value;
            const financialInstitution = document.getElementById('financialInstitution').value;

            // 確認ダイアログに入力情報を表示
            const confirmDetails = `
                <strong>お客様番号:</strong> ${customerNumber}<br>
                <strong>月分:</strong> ${paymentMonth}<br>
                <strong>支払日:</strong> ${paymentDate}<br>
                <strong>支払った金融機関:</strong> ${financialInstitution}
            `;
            document.getElementById('confirmDetails').innerHTML = confirmDetails;
            form.style.display = 'none';
            document.getElementById('confirmation').style.display = 'block';
        });

        // 確認ボタン処理
        document.getElementById('confirmButton').addEventListener('click', function () {
            const customerNumber = document.getElementById('customerNumber').value;
            const paymentMonth = document.getElementById('paymentMonth').value;
            const paymentDate = document.getElementById('paymentDate').value;
            const financialInstitution = document.getElementById('financialInstitution').value;

            // データをPHPに送信する
            fetch('save_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    customerNumber: customerNumber,
                    paymentMonth: paymentMonth,
                    paymentDate: paymentDate,
                    financialInstitution: financialInstitution
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // 完了メッセージを表示
                    document.getElementById('confirmation').style.display = 'none';
                    document.getElementById('completeMessage').style.display = 'block';
                } else {
                    alert("エラーが発生しました。もう一度試してください。");
                }
            });
        });
    });
</script>

</body>
</html>
