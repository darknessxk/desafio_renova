import {useMutation} from "@tanstack/react-query";
import {useUser} from "@/helper/user";
import {notification} from "antd";

export type AuthResponse = {
    token: string;
};

export type AuthRequest = {
    username: string;
    password: string;
};

export type UseLoginProps = {
    successCallback?: () => void;
    failureCallback?: () => void;
}

const useLogin = ({ successCallback, failureCallback }: UseLoginProps) => {
    const user = useUser();

    const { mutate, status } = useMutation<AuthResponse, Error, AuthRequest>({
        mutationFn: async ({ username, password }) => {
            if (!username || !password) {
                throw new Error('Username and password are required');
            }

            if (username.length < 3 || password.length < 6) {
                throw new Error('Username and password must be at least 6 characters');
            }

            if (user.isAuthenticated) {
                throw new Error('Already logged in');
            }

            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            });

            if (!response.ok) {
                const res = await response.json();
                throw new Error(res.error);
            }

            if (response.status === 204) {
                throw new Error('No content');
            }

            return await response.json();
        },
        onSuccess: (data) => {
            notification.success({
                message: 'Logged in',
                description: 'You are now logged in',
            });

            user.setToken(data.token);
            successCallback?.();

            return;
        },
        onError: (error) => {
            notification.error({
                message: 'Failed to login',
                description: error.message,
            });

            user.clearToken()

            failureCallback?.();
        },
    })

    return { mutate, status };
};

export default useLogin;