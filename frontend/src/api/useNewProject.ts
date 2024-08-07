"use client";
import {useMutation, useQueryClient} from "@tanstack/react-query";
import {useCallback, useState} from "react";
import {useUser} from "@/helper/user";
import {notification} from "antd";
import {useRouter} from "next/navigation";

export type NewProjectResponse = {
    id: string;
};

export type NewProjectRequest = {
    name?: string;
    description?: string;
    meta: number;
    category: string;
};

export type UseNewProjectProps = {
    successCallback?: () => void;
    failureCallback?: () => void;
}

const useNewProject = (props?: UseNewProjectProps) => {
    const { token } = useUser();
    const router = useRouter();
    const queryClient = useQueryClient();

    const {
        data,
        status,
        mutate
    } = useMutation({
        mutationFn: async (data: NewProjectRequest) => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/projects`, {
                method: 'PUT',
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

            return await response.json();
        },
        onSuccess: (ret: NewProjectResponse) => {
            if (props?.successCallback) {
                props?.successCallback();
            }

            queryClient.invalidateQueries({
                queryKey: ['projects'],
                stale: true,
                type: 'all'
            }).then()

            notification.success({
                message: 'Project created',
                description: 'The project has been created successfully',
                onClick: () => {
                    router.push(`/project/${ret.id}`);
                }
            });
        },
        onError: (error) => {
            if (props?.failureCallback) {
                props?.failureCallback();
            }

            notification.error({
                message: 'Failed to create project',
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

export default useNewProject;