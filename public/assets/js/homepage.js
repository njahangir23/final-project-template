console.log('calling homepage')

const bannerVideo = document.querySelector('.banner-video video');

function pauseVideo() {
  if (isElementInViewport(bannerVideo)) {
    bannerVideo.play();
  } else {
    bannerVideo.pause();
  }
}

function isElementInViewport(el) {
  const rect = el.getBoundingClientRect();
  return rect.top >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight);
}

window.addEventListener('scroll', pauseVideo);

const clientId = 'e6a4d02aecad433ca11ed7777f22818b';
const redirectUri = 'localhost';
let accessToken = '';

const generateBtn = document.getElementById('generateBtn');
const playlistDiv = document.getElementById('playlist');

function authenticateSpotify() {
    const scopes = 'playlist-read-private';
    const authUrl = `https://accounts.spotify.com/authorize?client_id=${clientId}&response_type=token&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${encodeURIComponent(scopes)}`;
    window.location.href = authUrl;
}

function getAccessTokenFromUrl() {
    const hash = window.location.hash.substring(1);
    const params = new URLSearchParams(hash);
    return params.get('access_token');
}

function fetchPlaylist(keyword) {
    fetch(`https://api.spotify.com/v1/search?q=${keyword}&type=playlist&limit=5`, {
        headers: {
            Authorization: `Bearer ${accessToken}`
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.playlists && data.playlists.items.length > 0) {
                displayPlaylists(data.playlists.items);
            } else {
                playlistDiv.innerHTML = '<p>No playlists found. Try a different word.</p>';
            }
        })
        .catch(err => {
            console.error(err);
            playlistDiv.innerHTML = '<p>Something went wrong. Please try again.</p>';
        });
}

function displayPlaylists(playlists) {
    playlistDiv.innerHTML = '';
    playlists.forEach(playlist => {
        const playlistItem = document.createElement('div');
        playlistItem.innerHTML = `
            <h3>${playlist.name}</h3>
            <img src="${playlist.images[0]?.url || 'https://via.placeholder.com/150'}" alt="${playlist.name}" width="150">
            <p><a href="${playlist.external_urls.spotify}" target="_blank">Open in Spotify</a></p>
        `;
        playlistDiv.appendChild(playlistItem);
    });
}

generateBtn.addEventListener('click', () => {
    const userInput = document.getElementById('userInput').value.trim();
    if (!accessToken) {
        alert('You need to authenticate with Spotify first.');
        authenticateSpotify();
    } else if (userInput) {
        fetchPlaylist(userInput);
    } else {
        alert('Please enter a word or mood.');
    }
});

window.onload = () => {
    const token = getAccessTokenFromUrl();
    if (token) {
        accessToken = token;
        window.history.pushState({}, document.title, '/'); // Clear the URL fragment
    }
};