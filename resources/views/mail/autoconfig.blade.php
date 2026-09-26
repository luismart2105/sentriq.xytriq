{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<clientConfig version="1.1">
    <emailProvider id="{{ $mailDomain }}">
        <domain>{{ $mailDomain }}</domain>
        <displayName>Sentriq</displayName>
        <displayShortName>Sentriq</displayShortName>
        <incomingServer type="imap">
            <hostname>{{ $mailHost }}</hostname>
            <port>993</port>
            <socketType>SSL</socketType>
            <authentication>password-cleartext</authentication>
            <username>%EMAILADDRESS%</username>
        </incomingServer>
        <outgoingServer type="smtp">
            <hostname>{{ $mailHost }}</hostname>
            <port>465</port>
            <socketType>SSL</socketType>
            <authentication>password-cleartext</authentication>
            <username>%EMAILADDRESS%</username>
        </outgoingServer>
    </emailProvider>
</clientConfig>
