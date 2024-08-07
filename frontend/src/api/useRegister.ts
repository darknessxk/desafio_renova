import {useMutation} from "@tanstack/react-query";
import {notification} from "antd";

export type RegisterResponse = {};

export type RegisterRequest = {
    email?: string;
    password?: string;
    firstName?: string;
    lastName?: string;
};

export type UseRegisterProps = {
    successCallback?: () => void;
    failureCallback?: () => void;
}

const useRegister = (props?: UseRegisterProps) => {
    const { mutate, status } = useMutation({
        mutationFn: async (args: RegisterRequest) => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/auth/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(args),
            });

            if (!response.ok) {
                const res = await response.json();
                throw new Error(res.error);
            }

            return await response.json();
        },

        onSuccess: () => {
            notification.success({
                message: 'Registered',
                description: 'You are now registered',
            });

            if (props?.successCallback) {
                props.successCallback();
            }
        },

        onError: (error) => {
            notification.error({
                message: 'Failed to register',
                description: error.message,
            });

            if (props?.failureCallback) {
                props.failureCallback();
            }
        },
    })

    return { mutate, status };
};

export default useRegister;