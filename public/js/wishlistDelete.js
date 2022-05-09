async function removeGame(gameID){
    console.log(gameID)

    await fetch(`/user/wishlist/${gameID}`, {
        method: 'delete',
        headers: {
            'Content-type': 'application/json'
        }
    });

    // Awaiting for the resource to be deleted
    const resData = 'resource deleted...';

    console.log(resData)

    window.location.replace('/user/wishlist')

}