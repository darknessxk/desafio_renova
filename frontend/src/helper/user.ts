"use client"
import useStorage from "@/helper/storage";
import {jwtDecode} from "@/helper/jwtDecode";
import {useEffect, useReducer} from "react";
import {useRouter} from "next/navigation";

interface JwtPayload {
    exp: number;
    iat: number;
    id: number;
    ip: string;
    roles: string[];
    username: string;
}

interface JwtUser {
    header: Record<string, string>;
    payload: JwtPayload;
}

interface UserState {
    user?: JwtUser;
    token: string | null;
    isAuthenticated: boolean;
}

enum UserActionType {
    setToken = "setToken",
    clearToken = "clearToken",
}

type UserActionSetToken = {
    type: UserActionType.setToken;
    payload: string;
}

type UserActionClearToken = {
    type: UserActionType.clearToken;
}

type UserActions = UserActionSetToken | UserActionClearToken;

function reducer(state: UserState, action: UserActions): UserState {
    const { type } = action;
    switch (type) {
        case UserActionType.setToken:
            const { payload } = action as UserActionSetToken;
            return {
                ...state,
                token: payload,
                user: jwtDecode(payload),
                isAuthenticated: true
            };
        case UserActionType.clearToken:
            return {
                ...state,
                token: null,
                isAuthenticated: false,
            };
        default:
            return state;
    }
}

export function useUser() {
    const storage = useStorage();

    const [state, dispatch] = useReducer(reducer, {
        token: "",
        isAuthenticated: false
    }, () => {
        const token = storage.get('user')
        if (token) {
            return {
                user: jwtDecode(token),
                token,
                isAuthenticated: true
            } as UserState;
        }

        return {
            token: "",
            isAuthenticated: false
        } as UserState;
    });

    useEffect(() => {
        document.addEventListener('storage', (event: any) => {
            if (event.key === 'user') {
                const token = storage.get('user');
                if (token) {
                    dispatch({ type: UserActionType.setToken, payload: token });
                } else {
                    dispatch({ type: UserActionType.clearToken });
                }
            }
        })
    })

    const router = useRouter();

    return {
        ...state,
        setToken: (token: string) => {
            storage.set('user', token);
            const event = new StorageEvent('storage', { key: 'user', newValue: token, oldValue: storage.get('user') });
            document.dispatchEvent(event);
            dispatch({ type: UserActionType.setToken, payload: token });
        },
        clearToken: () => {
            storage.remove('user');
            const event = new StorageEvent('storage', { key: 'user', newValue: null, oldValue: storage.get('user') });
            document.dispatchEvent(event);
            dispatch({ type: UserActionType.clearToken });
            router.push('/');
        },
    }
}
