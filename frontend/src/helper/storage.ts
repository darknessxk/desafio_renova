export default function useStorage() {
    function isServer() {
        return typeof window === "undefined";
    }

    return {
        get: (key: string) => {
            if (isServer()) { return null; }
            const value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        },
        set: (key: string, value: any) => {
            if (isServer()) { return; }
            localStorage.setItem(key, JSON.stringify(value));
        },
        remove: (key: string) => {
            if (isServer()) { return; }
            localStorage.removeItem(key);
        },
    };
}
