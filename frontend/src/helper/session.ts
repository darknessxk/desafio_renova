export default function useSession() {
    function isServer() {
        return typeof window === "undefined";
    }

    return {
        get: (key: string) => {
            if (isServer()) { return null; }
            const value = sessionStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        },
        set: (key: string, value: any) => {
            if (isServer()) { return; }
            sessionStorage.setItem(key, JSON.stringify(value));
        },
        remove: (key: string) => {
            if (isServer()) { return; }
            sessionStorage.removeItem(key);
        },
    };
}
