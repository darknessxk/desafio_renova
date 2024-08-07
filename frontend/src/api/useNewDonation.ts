"use client";
import {useMutation, useQueryClient} from "@tanstack/react-query";
import {notification} from "antd";

export type NewDonationResponse = {};

export type NewDonationRequest = {
    origin?: string;
    value: number;
};

export type UseNewDonationProps = {
    id: number;
    successCallback?: () => void;
    failureCallback?: () => void;
}

const useNewDonation = (props: UseNewDonationProps) => {
    const queryClient = useQueryClient();

    const {
        data,
        status,
        mutate
    } = useMutation({
        mutationFn: async (data: NewDonationRequest) => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/projects/${props.id}/donations`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                const res = await response.json();
                throw new Error(res.error);
            }

            return await response.json();
        },
        onSuccess: () => {
            if (props?.successCallback) {
                props?.successCallback();
            }

            queryClient.invalidateQueries({
                queryKey: ['project', props.id],
                stale: true,
                type: 'all'
            }).then()

            notification.success({
                message: 'Donation made',
                description: 'The donation was successfully made'
            });
        },
        onError: (error) => {
            if (props?.failureCallback) {
                props?.failureCallback();
            }

            notification.error({
                message: 'Failed to make donation',
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

export default useNewDonation;