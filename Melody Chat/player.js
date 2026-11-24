const accessToken = $_SESSION['spotify_access_token'];  // Substitua com seu token de acesso do Spotify

// Função para atualizar informações da música no player
function updatePlayer() {
    fetch('https://api.spotify.com/v1/me/player/currently-playing', {
        headers: {
            'Authorization': `Bearer ${accessToken}`
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.item) {
            const trackName = data.item.name;
            const artistName = data.item.artists[0].name;
            const albumCoverUrl = data.item.album.images[0].url;

            // Atualizando as informações no player
            document.getElementById('track-name').textContent = trackName;
            document.getElementById('artist-name').textContent = artistName;
            document.getElementById('album-cover').src = albumCoverUrl;
        } else {
            console.log('Nenhuma música tocando atualmente.');
        }
    })
    .catch(error => console.error('Erro ao buscar a música atual:', error));
}

// Atualiza o player a cada 5 segundos
setInterval(updatePlayer, 5000);

// Chama a função para carregar as informações da música quando o player for carregado
updatePlayer();