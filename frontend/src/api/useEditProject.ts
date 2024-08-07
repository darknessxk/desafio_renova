"use client";
import {useMutation, useQueryClient} from "@tanstack/react-query";
import {useUser} from "@/helper/user";
import {notification} from "antd";

export type NewProjectResponse = {};

export type NewProjectRequest = {
    name?: string;
    description?: string;
    meta: number;
    category: string;
};

export type UseNewProjectProps = {
    id: string;
    successCallback?: () => void;
    failureCallback?: () => void;
}

const useEditProject = (props: UseNewProjectProps) => {
    const { token } = useUser();
    const queryClient = useQueryClient();

    const {
        data,
        status,
        mutate
    } = useMutation({
        mutationFn: async (data: NewProjectRequest) => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/projects/${props.id}`, {
                method: 'POST',
                mode: 'cors',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                const res = await response.json();
                throw new Error(res.error);
            }

            return {};
        },
        onSuccess: () => {
            if (props?.successCallback) {
                props?.successCallback();
            }

            queryClient.invalidateQueries({
                queryKey: ['projects'],
                stale: true,
                type: 'all'
            }).then()

            notification.success({
                message: 'Project saved',
                description: 'The project has been edited'
            });
        },
        onError: (error) => {
            if (props?.failureCallback) {
                props?.failureCallback();
            }

            notification.error({
                message: 'Failed to edit project',
                description: error.message || 'An error occurred'
            });
        }
    })

    return {
        data,
        status,
        mutate
    }
};

export default useEditProject;