@component('mail::message')
# New Visitor Detected

**Date/Time:** {{ $data['datetime'] }}  

---

### 🧑‍💻 Device & Browser
- **Device:** {{ $data['device'] ?? 'Unknown' }}
- **Browser:** {{ $data['browser'] ?? 'Unknown' }}
- **OS:** {{ $data['os'] ?? 'Unknown' }}

### 📍 Location
- **IP:** {{ $data['ip'] ?? 'Unknown' }}
- **Country:** {{ $data['country'] ?? 'Unknown' }}
- **City:** {{ $data['city'] ?? 'Unknown' }}
- **Region:** {{ $data['region'] ?? 'Unknown' }}

### 🔗 Referrer
{{ $data['referrer'] ?? 'Direct' }}

### 🖥 Screen
{{ $data['screen'] ?? 'Unknown' }}

---

Thanks,  
{{ config('app.name') }}
@endcomponent