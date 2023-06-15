export default class LStorage
{

    constructor()
    {
        this.storage = localStorage;
    }

    save(key, val)
    {
        let jsonStr = JSON.stringify(val);
        try {
            this.storage.setItem(key, jsonStr);
        } catch (e) {
            alert(
                "Couldn't store items.\n"+
                "Perhaps, you have been reached to the limit of Local Storage.\n"+
                "Please make free space on Local Storage by deleteing unnecessary playlist after exporting to file."
            );
        }
    }

    remove(key)
    {
        this.storage.removeItem(key);
    }

    get(key)
    {
        return JSON.parse(this.storage.getItem(key));
    }

}
