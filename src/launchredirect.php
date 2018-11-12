<script>
    session = <?= json_encode($fe_session_data); ?>;
    if (window.opener) {
        window.opener.authorized(session);
        window.close();
    }
    if (window.parent) {
        window.parent.authorized(session);
    }

</script>