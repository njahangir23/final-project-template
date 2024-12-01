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
  var rect = el.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElemement.clientHeight)
    );
}

window.addEventListener('scroll', pauseVideo);