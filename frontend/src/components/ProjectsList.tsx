'use client';
import useBreakpoint from "antd/lib/grid/hooks/useBreakpoint";
import {useMemo} from "react";
import {Button, Col, Flex, Input, Row, Spin} from "antd";
import ProjectPreview from "@/components/ProjectPreview";
import {useProjects} from "@/api/useProjects";
import {useUser} from "@/helper/user";

type ProjectsListProps = {
    initialFilters?: Record<string, any>;
    owner?: number;
    headerContainer?: React.ReactNode;
}

const gutter = { xs: 8, sm: 16, md: 24, lg: 24 };

export default function ProjectsList({initialFilters, owner, headerContainer}: ProjectsListProps) {
    const {
        query: {
            data, error, status
        }, setFilter, filters, isBusy
    } = useProjects({
        initialFilters: initialFilters || {},
        owner
    });

    const user = useUser();
    const canEdit = useMemo(() => user?.user?.payload.id === owner, [user, owner]);

    const m = useBreakpoint()

    const colsPerRow = useMemo(() => (
        m.xxl ? 4 : m.xl ? 3 : m.lg ? 2 : 1
    ), [m]);
    
    const colSpan = useMemo(() => 24 / colsPerRow, [colsPerRow]);

    const transformedData = useMemo(() => (
        data?.map(project => (
            <Col key={project.id} span={colSpan}>
                <ProjectPreview edit={canEdit} project={project} setFilter={setFilter} />
            </Col>
        ))
    ), [data, colSpan, canEdit, setFilter]);

    return <>
        <Row gutter={gutter} className={"p-3 gap-2"}>
            {headerContainer}

            <Col>
                <Button
                    onClick={() => setFilter({})}
                    disabled={Object.keys(filters).length === 0}
                >
                    Reset filters
                </Button>
            </Col>

            <Col>
                <Input.Search
                    placeholder={"Search..."}
                    onSearch={(value) => setFilter({name: value})}
                />
            </Col>
        </Row>

        <Row gutter={gutter}>
            {
                (
                    isBusy
                        ? <Flex className={"m-10"}>
                            <Spin size="large"></Spin>
                        </Flex>
                        : status === 'error'
                            ? <Flex className={"m-10"}>
                                <p>Error: {error!.message}</p>
                            </Flex>
                            : transformedData
                )
            }
        </Row>
    </>
}