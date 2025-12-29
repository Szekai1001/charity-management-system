<x-mail::message>
# Welcome to the PKKM Team, {{ $user->name }}!

We are pleased to have you on board. Your teacher account for the **Persatuan Kebajikan Kasih Murni Batu Pahat (PKKM)** system has been successfully created.

You can now access the portal to manage student records and applications. Please find your temporary login credentials below:

<x-mail::panel>
**Email:** {{ $user->email }}  
**Temporary Password:** {{ $rawPassword }}
</x-mail::panel>

<x-mail::button :url="route('login')">
Login to PKKM Portal
</x-mail::button>

**Security Notice:** For the safety of our student data, please **change your password immediately** after logging in for the first time.

If you have any trouble logging in, please contact the PKKM Administration.

Best Regards,<br>
The Management<br>
**{{ config('app.name') }}**
</x-mail::message>