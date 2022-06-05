<script>
    const ogpFetch = (url) => {
        if (!String(url).startsWith('http')) return false;

        fetch('{{ route('admin.url.fetchOgp').'?url=' }}' + url)
            .then((res) => { return res.json() })
            .then((json) => {
                document.querySelector('input[name="url"]').value = json['og:url'] ?? '';
                document.querySelector('input[name="title"]').value = json['og:title'] ?? '';
                document.querySelector('textarea[name="description"]').value = json['og:description'] ?? '';
            });
    }
</script>
