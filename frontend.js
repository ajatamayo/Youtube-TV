(function($){

    var $playlists = $('.ca-youtube-playlist');

    $playlists.each(function(idx, el) {
        var $this = $(el),
            controller = new YTV($this.attr('id'), {
                playlist: $this.data('playlist'),
                apiKey: $this.data('api_key')
            });
    });

})(jQuery)
