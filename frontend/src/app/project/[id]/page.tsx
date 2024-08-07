'use client';

import useProject from "@/api/useProject";
import {Project} from "@/types/Project";
import ProjectPreview from "@/components/ProjectPreview";
import {Flex, Spin, Typography} from "antd";

type RouteParams = {
    id: string;
}

export default function Page({ params }: { params: RouteParams }) {
    const {
        isRefetching, isFetching, isLoading, isPending, data
    } = useProject(Number.parseInt(params.id));

    if (isRefetching || isFetching || isLoading || isPending) {
        return (
            <Flex>
                <Typography.Title level={3}>Loading project...</Typography.Title>
                <Spin />
            </Flex>
        )
    }

    return <>
        <ProjectPreview project={data as Project} full />
    </>
}
