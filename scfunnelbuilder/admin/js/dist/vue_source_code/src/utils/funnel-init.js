const getFunnelInit = () => {
  const urlString = window.location.href;
  let paramString = urlString.split("?")[1];
  let queryString = new URLSearchParams(paramString);
  let FunnelId = 0;
  for (let pair of queryString.entries()) {
    if (pair[0] === "id") {
      FunnelId = pair[1];
    }
  }
  return FunnelId ? FunnelId : null;
};

export { getFunnelInit };
