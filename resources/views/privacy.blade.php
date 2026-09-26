@extends('layouts.site')
@section('title', 'Aviso de privacidad')
@section('description', 'Aviso de privacidad para solicitudes de contacto de Sentriq.')
@section('content')
    <section class="page-hero"><div class="container page-hero__inner"><span class="eyebrow">Protección de datos personales</span><h1>Aviso de privacidad</h1><p>Conoce cómo Sentriq utiliza y protege los datos que compartes para solicitar atención.</p></div></section>
    <section class="section section--light">
        <div class="container legal-copy">
            <h2>1. Identidad y domicilio del responsable</h2>
            <p><strong>{{ config('sentriq.privacy.responsible_name') }}</strong>, quien opera comercialmente como Sentriq, es responsable del tratamiento de los datos personales recopilados mediante este sitio.</p>
            <p>Domicilio: {{ config('sentriq.privacy.responsible_address') }}.</p>

            <h2>2. Datos personales tratados</h2>
            <p>Podemos recopilar nombre, nombre del negocio, teléfono, correo electrónico, municipio o zona, servicio de interés y la descripción que decidas proporcionar. No solicitamos datos personales sensibles, financieros o patrimoniales mediante el formulario de contacto.</p>

            <h2>3. Finalidades del tratamiento</h2>
            <p>Utilizaremos los datos para responder tu solicitud, conocer de manera inicial el proyecto, comunicarnos contigo, programar seguimientos y, cuando corresponda, preparar una visita o cotización. Estas finalidades son necesarias para atender la solicitud que tú inicias.</p>
            <p>No utilizaremos los datos del formulario para publicidad masiva, perfiles comerciales automatizados ni finalidades secundarias incompatibles sin solicitar antes el consentimiento correspondiente.</p>

            <h2>4. Información obtenida por medios electrónicos</h2>
            <p>El sitio puede conservar durante la sesión los parámetros de campaña UTM y registrar la página de origen sin sus parámetros de consulta. También registra eventos anónimos de clic en enlaces a WhatsApp para medir su utilidad. Un clic no se considera una conversación ni crea por sí mismo una ficha de prospecto.</p>
            <p>El servidor puede generar registros técnicos necesarios para la operación y seguridad del sitio. No se realizan decisiones automatizadas que produzcan efectos jurídicos sobre las personas.</p>

            <h2>5. Transferencias y acceso por proveedores</h2>
            <p>Sentriq no vende datos personales ni contempla transferencias que requieran el consentimiento de la persona titular. Los proveedores que apoyen la operación técnica, alojamiento o correo podrán tratar la información por cuenta de Sentriq bajo instrucciones y medidas de confidencialidad. También podremos comunicar información cuando una autoridad competente lo requiera conforme a la ley.</p>

            <h2>6. Derechos ARCO y revocación del consentimiento</h2>
            <p>Puedes solicitar el acceso, rectificación, cancelación u oposición al tratamiento de tus datos, así como revocar tu consentimiento, escribiendo a <a href="mailto:{{ config('sentriq.contact.email') }}">{{ config('sentriq.contact.email') }}</a>.</p>
            <p>La solicitud debe incluir tu nombre y un medio para recibir la respuesta; documentación que acredite tu identidad o representación; una descripción clara de los datos relacionados con la solicitud; el derecho que deseas ejercer y, cuando resulte útil, cualquier dato que permita localizar tu registro. En una rectificación deberás indicar el cambio solicitado y adjuntar el sustento correspondiente.</p>
            <p>Comunicaremos la determinación adoptada en un plazo máximo de veinte días contados desde la recepción de la solicitud. Si resulta procedente, la haremos efectiva dentro de los quince días siguientes. Los plazos podrán ampliarse una sola vez por un periodo igual cuando las circunstancias lo justifiquen.</p>
            <p>Si consideras que tu derecho a la protección de datos personales ha sido vulnerado, puedes acudir ante la Secretaría Anticorrupción y Buen Gobierno en los términos de la legislación vigente.</p>

            <h2>7. Limitación del uso o divulgación</h2>
            <p>Puedes solicitar que limitemos el uso o divulgación de tus datos mediante el mismo correo. Atenderemos la solicitud siempre que no impida cumplir una obligación legal o contractual vigente.</p>

            <h2>8. Conservación y seguridad</h2>
            <p>Los datos de prospectos que no se conviertan en clientes se conservarán durante tres meses contados desde el último contacto y después serán eliminados o anonimizados. Si se establece una relación comercial, conservaremos la información durante los plazos necesarios para cumplir obligaciones contractuales, fiscales, administrativas o para atender posibles responsabilidades.</p>
            <p>Aplicamos medidas administrativas y técnicas razonables para evitar el acceso, uso, alteración o divulgación no autorizados.</p>

            <h2>9. Cambios al aviso</h2>
            <p>Las modificaciones a este aviso se publicarán en esta misma página. Cuando un cambio requiera un nuevo consentimiento, lo solicitaremos antes de aplicar el nuevo tratamiento.</p>
            <p><strong>Última actualización:</strong> 25 de septiembre de 2026.</p>
        </div>
    </section>
@endsection
